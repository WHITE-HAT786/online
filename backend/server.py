"""Depth Route Dialer - Backend API."""
from fastapi import FastAPI, APIRouter, HTTPException, Header, Depends
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field, EmailStr
from typing import List, Optional
import uuid
from datetime import datetime, timezone, timedelta
import bcrypt
import jwt
import httpx

ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

mongo_url = os.environ['MONGO_URL']
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ['DB_NAME']]

JWT_SECRET = os.environ.get("JWT_SECRET", "depth-route-secret-key-change-me")
JWT_ALGO = "HS256"
JWT_TTL_HOURS = 24 * 7

app = FastAPI(title="Depth Route Dialer")
api_router = APIRouter(prefix="/api")

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


# ------------ Models ------------
class LoginRequest(BaseModel):
    email: EmailStr
    password: str


class RegisterRequest(BaseModel):
    name: str
    email: EmailStr
    password: str


class GoogleSessionRequest(BaseModel):
    session_id: str


class User(BaseModel):
    user_id: str
    name: str
    email: str
    picture: Optional[str] = None
    role: str = "Administrator"
    created_at: str


class AuthResponse(BaseModel):
    token: str
    user: User


# ------------ Helpers ------------
def hash_password(pw: str) -> str:
    return bcrypt.hashpw(pw.encode(), bcrypt.gensalt()).decode()


def verify_password(pw: str, hashed: str) -> bool:
    try:
        return bcrypt.checkpw(pw.encode(), hashed.encode())
    except Exception:
        return False


def create_token(user_id: str) -> str:
    payload = {
        "sub": user_id,
        "exp": datetime.now(timezone.utc) + timedelta(hours=JWT_TTL_HOURS),
        "iat": datetime.now(timezone.utc),
    }
    return jwt.encode(payload, JWT_SECRET, algorithm=JWT_ALGO)


async def get_current_user(authorization: Optional[str] = Header(None)):
    if not authorization or not authorization.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Not authenticated")
    token = authorization.split(" ", 1)[1]
    # JWT flow
    try:
        payload = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGO])
        user = await db.users.find_one({"user_id": payload["sub"]}, {"_id": 0})
        if user:
            return user
    except Exception:
        pass
    # Emergent session flow
    session = await db.user_sessions.find_one({"session_token": token}, {"_id": 0})
    if not session:
        raise HTTPException(status_code=401, detail="Invalid token")
    expires_at = session.get("expires_at")
    if isinstance(expires_at, datetime):
        if expires_at.tzinfo is None:
            expires_at = expires_at.replace(tzinfo=timezone.utc)
        if expires_at < datetime.now(timezone.utc):
            raise HTTPException(status_code=401, detail="Session expired")
    user = await db.users.find_one({"user_id": session["user_id"]}, {"_id": 0})
    if not user:
        raise HTTPException(status_code=401, detail="User not found")
    return user


def user_public(u: dict) -> dict:
    return {
        "user_id": u["user_id"],
        "name": u.get("name", ""),
        "email": u.get("email", ""),
        "picture": u.get("picture"),
        "role": u.get("role", "Administrator"),
        "created_at": u.get("created_at", ""),
    }


# ------------ Startup: seed demo user + mock data ------------
@app.on_event("startup")
async def startup_seed():
    # Indexes
    try:
        await db.users.create_index("email", unique=True)
        await db.users.create_index("user_id", unique=True)
        await db.user_sessions.create_index("session_token", unique=True)
    except Exception as e:
        logger.warning(f"Index error: {e}")

    # Seed demo user
    existing = await db.users.find_one({"email": "demo@depthroute.com"})
    if not existing:
        await db.users.insert_one({
            "user_id": "user_demo_001",
            "name": "John Doe",
            "email": "demo@depthroute.com",
            "password": hash_password("demo1234"),
            "role": "Administrator",
            "picture": None,
            "phone": "+1 (555) 123-4567",
            "username": "johndoe",
            "created_at": datetime.now(timezone.utc).isoformat(),
        })
        logger.info("Seeded demo user demo@depthroute.com / demo1234")


# ------------ Auth Endpoints ------------
@api_router.post("/auth/register", response_model=AuthResponse)
async def register(req: RegisterRequest):
    existing = await db.users.find_one({"email": req.email})
    if existing:
        raise HTTPException(status_code=400, detail="Email already registered")
    uid = f"user_{uuid.uuid4().hex[:12]}"
    doc = {
        "user_id": uid,
        "name": req.name,
        "email": req.email,
        "password": hash_password(req.password),
        "role": "Administrator",
        "picture": None,
        "created_at": datetime.now(timezone.utc).isoformat(),
    }
    await db.users.insert_one(doc)
    token = create_token(uid)
    return {"token": token, "user": user_public(doc)}


@api_router.post("/auth/login", response_model=AuthResponse)
async def login(req: LoginRequest):
    user = await db.users.find_one({"email": req.email}, {"_id": 0})
    if not user or not user.get("password") or not verify_password(req.password, user["password"]):
        raise HTTPException(status_code=401, detail="Invalid email or password")
    token = create_token(user["user_id"])
    return {"token": token, "user": user_public(user)}


@api_router.post("/auth/google/session")
async def google_session(req: GoogleSessionRequest):
    """Complete Google OAuth using Emergent session-id."""
    async with httpx.AsyncClient(timeout=15.0) as http:
        r = await http.get(
            "https://demobackend.emergentagent.com/auth/v1/env/oauth/session-data",
            headers={"X-Session-ID": req.session_id},
        )
        if r.status_code != 200:
            raise HTTPException(status_code=401, detail="Invalid session_id")
        data = r.json()
    email = data.get("email")
    name = data.get("name") or "User"
    picture = data.get("picture")
    session_token = data.get("session_token")
    if not email or not session_token:
        raise HTTPException(status_code=400, detail="Bad session data")

    existing = await db.users.find_one({"email": email}, {"_id": 0})
    if existing:
        uid = existing["user_id"]
        if not existing.get("picture") and picture:
            await db.users.update_one({"user_id": uid}, {"$set": {"picture": picture}})
        existing["picture"] = picture
        user_doc = existing
    else:
        uid = f"user_{uuid.uuid4().hex[:12]}"
        user_doc = {
            "user_id": uid,
            "name": name,
            "email": email,
            "picture": picture,
            "role": "Administrator",
            "created_at": datetime.now(timezone.utc).isoformat(),
        }
        await db.users.insert_one(user_doc)

    await db.user_sessions.update_one(
        {"session_token": session_token},
        {"$set": {
            "session_token": session_token,
            "user_id": uid,
            "expires_at": datetime.now(timezone.utc) + timedelta(days=7),
            "created_at": datetime.now(timezone.utc),
        }},
        upsert=True,
    )
    return {"token": session_token, "user": user_public(user_doc)}


@api_router.get("/auth/me")
async def me(user=Depends(get_current_user)):
    return user_public(user)


@api_router.post("/auth/logout")
async def logout(authorization: Optional[str] = Header(None)):
    if authorization and authorization.startswith("Bearer "):
        token = authorization.split(" ", 1)[1]
        await db.user_sessions.delete_one({"session_token": token})
    return {"ok": True}


# ------------ Mock data endpoints ------------
@api_router.get("/dashboard")
async def dashboard(user=Depends(get_current_user)):
    return {
        "profile": {
            "name": user.get("name", "John Doe"),
            "ext": "1001",
            "sip_status": "SIP Registered",
            "picture": user.get("picture"),
        },
        "balance": {"amount": 125.45, "currency": "USD"},
        "plan": {"name": "Professional Plan", "valid_till": "20 Jun 2025"},
        "stats": [
            {"label": "Total Calls", "value": "1,245", "change": "+12%", "positive": True, "icon": "outgoing"},
            {"label": "Incoming Calls", "value": "642", "change": "+8%", "positive": True, "icon": "incoming"},
            {"label": "Outgoing Calls", "value": "603", "change": "+15%", "positive": True, "icon": "outgoing"},
            {"label": "Missed Calls", "value": "87", "change": "-5%", "positive": False, "icon": "missed"},
        ],
        "recent_calls": [
            {"id": "1", "name": "Alice Smith", "ext": "1002", "type": "outgoing", "time": "10:30 AM", "duration": "02:15"},
            {"id": "2", "name": "Michael Brown", "ext": "1003", "type": "incoming", "time": "10:18 AM", "duration": "01:42"},
            {"id": "3", "name": "+1 202-555-0199", "ext": None, "type": "missed", "time": "10:05 AM", "duration": "00:00"},
            {"id": "4", "name": "Sarah Johnson", "ext": "1004", "type": "outgoing", "time": "09:52 AM", "duration": "03:21"},
            {"id": "5", "name": "David Wilson", "ext": "1005", "type": "incoming", "time": "09:41 AM", "duration": "01:05"},
        ],
        "quick_stats": [
            {"label": "Active Calls", "value": "3", "sub": "Live Calls", "icon": "headset", "color": "#14B8A6"},
            {"label": "SIP Accounts", "value": "2", "sub": "Registered", "icon": "layers", "color": "#3B82F6"},
            {"label": "Voicemails", "value": "5", "sub": "New Messages", "icon": "voicemail", "color": "#A855F7"},
        ],
    }


@api_router.get("/contacts")
async def contacts(user=Depends(get_current_user)):
    return {
        "stats": {"all": 256, "favorites": 18, "groups": 12},
        "items": [
            {"id": "1", "name": "Aaron Anderson", "phone": "+1 202-555-0123", "avatar_color": "#A855F7", "favorite": False},
            {"id": "2", "name": "Alice Johnson", "phone": "+1 202-555-0143", "avatar_color": "#3B82F6", "favorite": True},
            {"id": "3", "name": "Mike Anderson", "phone": "+1 202-555-0177", "avatar_color": "#3B82F6", "favorite": False},
            {"id": "4", "name": "Anna Smith", "phone": "+1 202-555-0189", "avatar_color": "#14B8A6", "favorite": False},
            {"id": "5", "name": "Bob Brown", "phone": "+1 202-555-0199", "avatar_color": "#F59E0B", "favorite": True},
            {"id": "6", "name": "Brian Taylor", "phone": "+1 202-555-0111", "avatar_color": "#F59E0B", "favorite": False},
            {"id": "7", "name": "Brittany White", "phone": "+1 202-555-0166", "avatar_color": "#EC4899", "favorite": True},
            {"id": "8", "name": "Charles Scott", "phone": "+1 202-555-0133", "avatar_color": "#A855F7", "favorite": False},
            {"id": "9", "name": "David Wilson", "phone": "+1 202-555-0155", "avatar_color": "#3B82F6", "favorite": False},
            {"id": "10", "name": "Emma Martinez", "phone": "+1 202-555-0122", "avatar_color": "#EC4899", "favorite": False},
        ],
    }


@api_router.get("/call-logs")
async def call_logs(user=Depends(get_current_user)):
    return {
        "stats": [
            {"label": "Total Calls", "value": "128", "change": "+12%", "positive": True, "icon": "outgoing"},
            {"label": "Incoming", "value": "40", "change": "+10%", "positive": True, "icon": "incoming"},
            {"label": "Outgoing", "value": "72", "change": "+15%", "positive": True, "icon": "outgoing"},
            {"label": "Missed", "value": "16", "change": "-5%", "positive": False, "icon": "missed"},
        ],
        "items": [
            {"id": "1", "name": "John Anderson", "number": "+1 202-555-0187", "type": "outgoing", "trunk": "Telnyx US Trunk", "time": "10:28 AM", "duration": "02:15"},
            {"id": "2", "name": "Alice Smith", "number": "+1 202-555-0143", "type": "incoming", "trunk": "Telnyx US Trunk", "time": "10:18 AM", "duration": "01:42"},
            {"id": "3", "name": "+1 202-555-0199", "number": "+1 202-555-0199", "type": "missed", "trunk": "Twilio Trunk", "time": "10:04 AM", "duration": "00:00"},
            {"id": "4", "name": "Mike Brown", "number": "+1 202-555-0123", "type": "outgoing", "trunk": "Telnyx US Trunk", "time": "09:52 AM", "duration": "03:21"},
            {"id": "5", "name": "+1 202-555-0177", "number": "+1 202-555-0177", "type": "incoming", "trunk": "Bandwidth Trunk", "time": "09:41 AM", "duration": "01:05"},
            {"id": "6", "name": "Sarah Johnson", "number": "+1 202-555-0166", "type": "outgoing", "trunk": "Telnyx US Trunk", "time": "09:32 AM", "duration": "04:11"},
            {"id": "7", "name": "+1 202-555-0118", "number": "+1 202-555-0118", "type": "missed", "trunk": "Twilio Trunk", "time": "09:16 AM", "duration": "00:00"},
            {"id": "8", "name": "David Wilson", "number": "+1 202-555-0105", "type": "incoming", "trunk": "Bandwidth Trunk", "time": "08:58 AM", "duration": "02:33"},
        ],
    }


@api_router.get("/voicemails")
async def voicemails(user=Depends(get_current_user)):
    return {
        "stats": {"all": 23, "new": 5, "saved": 12, "deleted": 6},
        "storage": {"used_mb": 23, "total_mb": 100, "percent": 23},
        "items": [
            {"id": "1", "name": "Alice Smith", "ext": "1002", "date": "28 Jul 2025, 10:30 AM", "duration": "00:18", "new": True, "color": "#A855F7"},
            {"id": "2", "name": "Michael Brown", "ext": "1003", "date": "28 Jul 2025, 10:18 AM", "duration": "00:27", "new": True, "color": "#22C55E"},
            {"id": "3", "name": "Sarah Johnson", "ext": "1004", "date": "28 Jul 2025, 09:52 AM", "duration": "00:15", "new": False, "color": "#F59E0B"},
            {"id": "4", "name": "David Wilson", "ext": "1005", "date": "28 Jul 2025, 09:41 AM", "duration": "00:21", "new": False, "color": "#3B82F6"},
            {"id": "5", "name": "James Anderson", "ext": "1006", "date": "28 Jul 2025, 09:15 AM", "duration": "00:35", "new": False, "color": "#EF4444"},
        ],
    }


@api_router.get("/sms")
async def sms(user=Depends(get_current_user)):
    return {
        "stats": {"total_sent": 1245, "delivered": 1186, "delivery_rate": 95.26, "sms_balance": 12450},
        "recent": [
            {"id": "1", "number": "+1 202-555-0187", "message": "Your verification code is 123456.", "time": "10:30 AM", "status": "Delivered", "color": "#22C55E"},
            {"id": "2", "number": "+1 305-555-0148", "message": "Thank you for contacting us.", "time": "Yesterday", "status": "Delivered", "color": "#A855F7"},
            {"id": "3", "number": "+1 786-555-0199", "message": "Your appointment is confirmed for tomorrow.", "time": "Jul 27, 2025", "status": "Delivered", "color": "#F59E0B"},
            {"id": "4", "number": "+1 929-555-0102", "message": "Special offer for you! Get 20% off on your next purchase.", "time": "Jul 26, 2025", "status": "Pending", "color": "#3B82F6"},
        ],
    }


@api_router.get("/recordings")
async def recordings(user=Depends(get_current_user)):
    return {
        "stats": {"all": 28, "calls": 22, "voicemails": 6, "total_duration": "02:45:30"},
        "storage": {"used_gb": 1.2, "total_gb": 5, "percent": 24},
        "items": [
            {"id": "1", "name": "Alice Smith", "ext": "1002", "direction": "→ John Doe (1001)", "date": "28 Jul 2025, 10:30 AM", "duration": "02:15", "type": "Call Recording", "color": "#A855F7", "wave": "#A855F7"},
            {"id": "2", "name": "Michael Brown", "ext": "1003", "direction": "← You (1001)", "date": "28 Jul 2025, 10:18 AM", "duration": "01:42", "type": "Call Recording", "color": "#22C55E", "wave": "#22C55E"},
            {"id": "3", "name": "Sarah Johnson", "ext": "1004", "direction": "→ John Doe (1001)", "date": "28 Jul 2025, 09:52 AM", "duration": "03:21", "type": "Call Recording", "color": "#F59E0B", "wave": "#F59E0B"},
            {"id": "4", "name": "Voicemail from +1 202-555-0199", "ext": None, "direction": "", "date": "28 Jul 2025, 09:41 AM", "duration": "00:48", "type": "Voicemail", "color": "#A855F7", "wave": "#A855F7"},
            {"id": "5", "name": "David Wilson", "ext": "1005", "direction": "← You (1001)", "date": "28 Jul 2025, 09:15 AM", "duration": "04:12", "type": "Call Recording", "color": "#3B82F6", "wave": "#3B82F6"},
        ],
    }


@api_router.get("/sip-accounts")
async def sip_accounts(user=Depends(get_current_user)):
    return {
        "stats": {"total": 128, "active": 96, "inactive": 24, "disabled": 8},
        "items": [
            {"id": "1", "name": "John Smith", "username": "1001", "domain": "sip.depthroute.com", "ip": "192.168.1.10", "port": 5060, "status": "Active", "color": "#3B82F6"},
            {"id": "2", "name": "Alice Davis", "username": "1002", "domain": "sip.depthroute.com", "ip": "192.168.1.11", "port": 5060, "status": "Active", "color": "#A855F7"},
            {"id": "3", "name": "Michael Roberts", "username": "1003", "domain": "sip.depthroute.com", "ip": "192.168.1.12", "port": 5060, "status": "Inactive", "color": "#14B8A6"},
            {"id": "4", "name": "Sarah Wilson", "username": "1004", "domain": "sip.depthroute.com", "ip": "192.168.1.13", "port": 5060, "status": "Active", "color": "#F59E0B"},
            {"id": "5", "name": "David Lee", "username": "1005", "domain": "sip.depthroute.com", "ip": "192.168.1.14", "port": 5060, "status": "Disabled", "color": "#EF4444"},
            {"id": "6", "name": "Chris Walker", "username": "1006", "domain": "sip.depthroute.com", "ip": "192.168.1.15", "port": 5060, "status": "Active", "color": "#3B82F6"},
            {"id": "7", "name": "Emma Martinez", "username": "1007", "domain": "sip.depthroute.com", "ip": "192.168.1.16", "port": 5060, "status": "Inactive", "color": "#A855F7"},
            {"id": "8", "name": "James Brown", "username": "1008", "domain": "sip.depthroute.com", "ip": "192.168.1.17", "port": 5060, "status": "Active", "color": "#14B8A6"},
        ],
    }


@api_router.get("/extensions")
async def extensions(user=Depends(get_current_user)):
    return {
        "stats": {"total": 56, "active": 42, "inactive": 10, "disabled": 4},
        "items": [
            {"id": "1", "ext": "1001", "name": "John Smith", "email": "john.smith@depthroute.com", "device": "SIP Phone", "caller_id": "+1 202-555-0101", "status": "Active", "color": "#3B82F6"},
            {"id": "2", "ext": "1002", "name": "Alice Davis", "email": "alice.davis@depthroute.com", "device": "Softphone", "caller_id": "+1 202-555-0102", "status": "Active", "color": "#A855F7"},
            {"id": "3", "ext": "1003", "name": "Michael Roberts", "email": "michael.roberts@depthroute.com", "device": "SIP Phone", "caller_id": "+1 202-555-0103", "status": "Inactive", "color": "#14B8A6"},
            {"id": "4", "ext": "1004", "name": "Sarah Wilson", "email": "sarah.wilson@depthroute.com", "device": "Softphone", "caller_id": "+1 202-555-0104", "status": "Active", "color": "#F59E0B"},
            {"id": "5", "ext": "1005", "name": "David Lee", "email": "david.lee@depthroute.com", "device": "SIP Phone", "caller_id": "+1 202-555-0105", "status": "Disabled", "color": "#EF4444"},
            {"id": "6", "ext": "1006", "name": "Chris Walker", "email": "chris.walker@depthroute.com", "device": "SIP Phone", "caller_id": "+1 202-555-0106", "status": "Active", "color": "#3B82F6"},
            {"id": "7", "ext": "1007", "name": "Emma Martinez", "email": "emma.martinez@depthroute.com", "device": "Softphone", "caller_id": "+1 202-555-0107", "status": "Inactive", "color": "#A855F7"},
            {"id": "8", "ext": "1008", "name": "James Brown", "email": "james.brown@depthroute.com", "device": "SIP Phone", "caller_id": "+1 202-555-0108", "status": "Active", "color": "#14B8A6"},
        ],
    }


@api_router.get("/numbers")
async def numbers(user=Depends(get_current_user)):
    return {
        "stats": {"total": 1256, "active": 1102, "in_use": 98, "inactive": 56},
        "items": [
            {"id": "1", "number": "+1 202-555-0101", "type": "Local", "location": "Washington, DC", "status": "In Use", "assigned": "1001 (John Smith)", "color": "#3B82F6"},
            {"id": "2", "number": "+1 305-555-0148", "type": "Local", "location": "Florida, Miami", "status": "Active", "assigned": "1002 (Alice Davis)", "color": "#22C55E"},
            {"id": "3", "number": "+1 800-555-0199", "type": "Toll Free", "location": "Toll Free Number", "status": "In Use", "assigned": "1003 (Michael Roberts)", "color": "#F59E0B"},
            {"id": "4", "number": "+1 312-555-0177", "type": "Local", "location": "Illinois, Chicago", "status": "Active", "assigned": "1004 (Sarah Wilson)", "color": "#22C55E"},
            {"id": "5", "number": "+1 469-555-0112", "type": "Local", "location": "Texas, Dallas", "status": "Inactive", "assigned": "Not Assigned", "color": "#EF4444"},
            {"id": "6", "number": "+1 408-555-0133", "type": "Local", "location": "California, San Jose", "status": "Active", "assigned": "1005 (David Lee)", "color": "#22C55E"},
            {"id": "7", "number": "+1 646-555-0188", "type": "Local", "location": "New York, New York", "status": "Active", "assigned": "1006 (Chris Walker)", "color": "#22C55E"},
            {"id": "8", "number": "+1 877-555-0166", "type": "Toll Free", "location": "Toll Free Number", "status": "In Use", "assigned": "1007 (Emma Martinez)", "color": "#F59E0B"},
        ],
    }


@api_router.get("/ivr")
async def ivr(user=Depends(get_current_user)):
    return {
        "stats": {"total": 28, "active": 20, "inactive": 6, "disabled": 2},
        "items": [
            {"id": "1", "name": "Sales IVR", "ext": "5001", "created": "May 10, 2024 10:30 AM", "steps": 8, "calls_today": 124, "status": "Active", "icon": "cart", "color": "#EC4899"},
            {"id": "2", "name": "Support IVR", "ext": "5002", "created": "May 08, 2024 02:15 PM", "steps": 6, "calls_today": 98, "status": "Active", "icon": "headset", "color": "#22C55E"},
            {"id": "3", "name": "Payment IVR", "ext": "5003", "created": "May 05, 2024 11:20 AM", "steps": 7, "calls_today": 32, "status": "Inactive", "icon": "cash", "color": "#F97316"},
            {"id": "4", "name": "Info IVR", "ext": "5004", "created": "May 03, 2024 09:45 AM", "steps": 5, "calls_today": 76, "status": "Active", "icon": "information", "color": "#3B82F6"},
            {"id": "5", "name": "Registration IVR", "ext": "5005", "created": "Apr 30, 2024 04:10 PM", "steps": 9, "calls_today": 18, "status": "Inactive", "icon": "person", "color": "#EC4899"},
            {"id": "6", "name": "Campaign IVR", "ext": "5006", "created": "Apr 28, 2024 01:30 PM", "steps": 4, "calls_today": 0, "status": "Disabled", "icon": "megaphone", "color": "#F59E0B"},
            {"id": "7", "name": "Banking IVR", "ext": "5007", "created": "Apr 25, 2024 10:05 AM", "steps": 10, "calls_today": 205, "status": "Active", "icon": "bank", "color": "#14B8A6"},
            {"id": "8", "name": "Travel IVR", "ext": "5008", "created": "Apr 22, 2024 03:50 PM", "steps": 6, "calls_today": 27, "status": "Inactive", "icon": "airplane", "color": "#A855F7"},
        ],
    }


@api_router.get("/plans")
async def plans(user=Depends(get_current_user)):
    return {
        "stats": {"total": 42, "active": 30, "inactive": 8, "disabled": 4},
        "items": [
            {"id": "1", "name": "Starter Plan", "category": "Retail", "minutes": "1000 mins", "accounts": "1 SIP Account", "concurrent": "1 Concurrent Call", "created": "May 10, 2024", "price": 10.00, "status": "Active", "icon": "rocket", "color": "#3B82F6"},
            {"id": "2", "name": "Basic Plan", "category": "Retail", "minutes": "3000 mins", "accounts": "2 SIP Accounts", "concurrent": "2 Concurrent Calls", "created": "May 08, 2024", "price": 25.00, "status": "Active", "icon": "star", "color": "#22C55E"},
            {"id": "3", "name": "Professional Plan", "category": "Retail", "minutes": "6000 mins", "accounts": "5 SIP Accounts", "concurrent": "3 Concurrent Calls", "created": "May 05, 2024", "price": 45.00, "status": "Active", "icon": "crown", "color": "#A855F7"},
            {"id": "4", "name": "Business Plan", "category": "Retail", "minutes": "12000 mins", "accounts": "10 SIP Accounts", "concurrent": "5 Concurrent Calls", "created": "Apr 30, 2024", "price": 75.00, "status": "Inactive", "icon": "building", "color": "#F97316"},
            {"id": "5", "name": "Call Center Plan", "category": "Call Center", "minutes": "Unlimited mins", "accounts": "20 SIP Accounts", "concurrent": "10 Concurrent Calls", "created": "Apr 28, 2024", "price": 150.00, "status": "Active", "icon": "headset", "color": "#EF4444"},
            {"id": "6", "name": "Wholesale Silver", "category": "Wholesale", "minutes": "Unlimited mins", "accounts": "50 SIP Accounts", "concurrent": "20 Concurrent Calls", "created": "Apr 25, 2024", "price": 200.00, "status": "Active", "icon": "handshake", "color": "#14B8A6"},
            {"id": "7", "name": "Wholesale Gold", "category": "Wholesale", "minutes": "Unlimited mins", "accounts": "100 SIP Accounts", "concurrent": "50 Concurrent Calls", "created": "Apr 22, 2024", "price": 350.00, "status": "Inactive", "icon": "medal", "color": "#F59E0B"},
            {"id": "8", "name": "Trial Plan", "category": "Retail", "minutes": "100 mins", "accounts": "1 SIP Account", "concurrent": "1 Concurrent Call", "created": "Apr 20, 2024", "price": 0.00, "status": "Disabled", "icon": "ban", "color": "#6B7280"},
        ],
    }


@api_router.get("/billing")
async def billing(user=Depends(get_current_user)):
    return {
        "stats": {
            "total_balance": 52345.20, "paid": 43245.10, "unpaid": 9100.10, "overdue": 2950.20,
            "paid_change": "+15.2%", "unpaid_change": "+8.4%", "overdue_change": "+6.3%", "total_change": "+15.6%",
        },
        "invoices": [
            {"id": "INV-2024-0154", "client": "ABC Solutions LLC", "date": "May 25, 2024", "due": "Jun 10, 2024", "amount": 1250.00, "status": "Paid"},
            {"id": "INV-2024-0153", "client": "Tech Connect Inc.", "date": "May 24, 2024", "due": "Jun 08, 2024", "amount": 850.00, "status": "Unpaid"},
            {"id": "INV-2024-0152", "client": "Global Voice Pvt Ltd", "date": "May 23, 2024", "due": "Jun 06, 2024", "amount": 2300.00, "status": "Paid"},
            {"id": "INV-2024-0151", "client": "Fast Call Solutions", "date": "May 22, 2024", "due": "Jun 05, 2024", "amount": 1150.00, "status": "Overdue"},
            {"id": "INV-2024-0150", "client": "Nextgen Systems", "date": "May 20, 2024", "due": "Jun 03, 2024", "amount": 950.00, "status": "Paid"},
        ],
        "summary": {"total": 154, "paid": 102, "unpaid": 36, "overdue": 16},
        "payment_methods": [
            {"method": "Bank Transfer", "percent": 45.2},
            {"method": "Credit / Debit Card", "percent": 30.1},
            {"method": "PayPal", "percent": 15.6},
            {"method": "Other", "percent": 9.1},
        ],
    }


@api_router.get("/reports")
async def reports(user=Depends(get_current_user)):
    return {
        "stats": [
            {"label": "Total Calls", "value": "12,458", "change": "+12.5%", "positive": True, "sub": "vs May 13 - May 19", "icon": "phone"},
            {"label": "Total Minutes", "value": "18,742", "change": "+8.3%", "positive": True, "sub": "vs May 13 - May 19", "icon": "trending"},
            {"label": "Total Cost", "value": "$2,456.75", "change": "-3.6%", "positive": True, "sub": "vs May 13 - May 19", "icon": "cash"},
            {"label": "Avg. Call Duration", "value": "01:30", "change": "+6.7%", "positive": True, "sub": "vs May 13 - May 19", "icon": "chart"},
        ],
        "call_activity": {
            "labels": ["May 20", "May 21", "May 22", "May 23", "May 24", "May 25", "May 26"],
            "total_calls": [1000, 1600, 2400, 1500, 2200, 1200, 900],
            "total_minutes": [1600, 2400, 4000, 2200, 3800, 2000, 1600],
        },
        "direction": {"outbound": 7548, "inbound": 3652, "internal": 1258, "total": 12458},
        "destinations": [
            {"country": "USA", "flag": "US", "calls": 3245, "minutes": 4782, "cost": 612.45},
            {"country": "India", "flag": "IN", "calls": 2105, "minutes": 3256, "cost": 312.50},
            {"country": "Canada", "flag": "CA", "calls": 1256, "minutes": 2145, "cost": 245.10},
            {"country": "UK", "flag": "GB", "calls": 985, "minutes": 1542, "cost": 185.30},
            {"country": "Australia", "flag": "AU", "calls": 742, "minutes": 1102, "cost": 125.60},
        ],
        "trunks": [
            {"trunk": "Trunk Group 1", "calls": 3245, "minutes": 4782, "cost": 612.45, "asr": 82.6, "acd": "01:28", "pdd": "00:03"},
            {"trunk": "Trunk Group 2", "calls": 2856, "minutes": 4125, "cost": 542.30, "asr": 78.3, "acd": "01:26", "pdd": "00:04"},
            {"trunk": "Trunk Group 3", "calls": 2124, "minutes": 3256, "cost": 398.10, "asr": 76.5, "acd": "01:32", "pdd": "00:03"},
            {"trunk": "Trunk Group 4", "calls": 1569, "minutes": 2145, "cost": 245.20, "asr": 79.1, "acd": "01:22", "pdd": "00:02"},
            {"trunk": "Trunk Group 5", "calls": 1132, "minutes": 1102, "cost": 124.70, "asr": 75.8, "acd": "01:35", "pdd": "00:04"},
        ],
    }


@api_router.get("/notifications")
async def notifications(user=Depends(get_current_user)):
    return {
        "items": [
            {"id": "1", "title": "New Call Alert", "body": "New call from +1 (555) 123-4567 to 1001", "time": "Just now", "unread": True, "icon": "call", "color": "#22C55E", "category": "Account"},
            {"id": "2", "title": "SIP Registration", "body": "SIP Account \"sales_trunk\" is now registered.", "time": "2 min ago", "unread": True, "icon": "people", "color": "#3B82F6", "category": "System"},
            {"id": "3", "title": "Payment Received", "body": "Payment of $250.00 received from ABC Solutions LLC", "time": "15 min ago", "unread": True, "icon": "receipt", "color": "#F59E0B", "category": "Billing"},
            {"id": "4", "title": "Trunk Status", "body": "Trunk \"TWILIO-1\" is now UP", "time": "45 min ago", "unread": True, "icon": "server", "color": "#A855F7", "category": "System"},
            {"id": "5", "title": "Low Balance Alert", "body": "Your account balance is low. Please recharge.", "time": "1 hr ago", "unread": True, "icon": "warning", "color": "#EF4444", "category": "Billing"},
            {"id": "6", "title": "Security Alert", "body": "New login detected from Chrome on Windows", "time": "3 hrs ago", "unread": False, "icon": "shield-check", "color": "#22C55E", "category": "Security"},
            {"id": "7", "title": "Invoice Generated", "body": "Invoice INV-2024-0148 has been generated", "time": "May 25, 2024 10:30 AM", "unread": False, "icon": "document", "color": "#3B82F6", "category": "Billing"},
            {"id": "8", "title": "System Update", "body": "System maintenance scheduled on May 26, 2024 02:00 AM", "time": "May 24, 2024 05:00 PM", "unread": False, "icon": "cog", "color": "#F97316", "category": "System"},
            {"id": "9", "title": "New Extension Created", "body": "Extension 1005 has been created successfully", "time": "May 24, 2024 03:20 PM", "unread": False, "icon": "person-add", "color": "#A855F7", "category": "Account"},
            {"id": "10", "title": "Missed Call", "body": "Missed call from +1 (555) 987-6543", "time": "May 24, 2024 01:10 PM", "unread": False, "icon": "call-missed", "color": "#3B82F6", "category": "Account"},
            {"id": "11", "title": "Auto Recharge", "body": "Account auto recharged with $100.00", "time": "May 24, 2024 11:00 AM", "unread": False, "icon": "card-plus", "color": "#22C55E", "category": "Billing"},
        ],
    }


@api_router.get("/support")
async def support(user=Depends(get_current_user)):
    return {
        "topics": [
            {"id": "1", "title": "Getting Started", "sub": "Learn the basics and get started quickly", "icon": "people", "color": "#3B82F6"},
            {"id": "2", "title": "SIP Accounts", "sub": "Manage SIP accounts and configurations", "icon": "layers", "color": "#22C55E"},
            {"id": "3", "title": "IVR & Extensions", "sub": "Setup IVR, extensions and call routing", "icon": "call", "color": "#A855F7"},
            {"id": "4", "title": "Billing & Payments", "sub": "Invoices, payments, and account balance", "icon": "receipt", "color": "#F59E0B"},
            {"id": "5", "title": "Security & Troubleshooting", "sub": "Security settings and troubleshoot issues", "icon": "shield", "color": "#14B8A6"},
        ],
        "tickets": [
            {"id": "#TKT-2024-0154", "title": "SIP Registration Issue", "created": "May 25, 2024", "updated": "May 25, 2024", "status": "Open"},
            {"id": "#TKT-2024-0153", "title": "Payment not reflected", "created": "May 24, 2024", "updated": "May 24, 2024", "status": "In Progress"},
        ],
    }


@api_router.get("/profile")
async def profile(user=Depends(get_current_user)):
    return {
        "user": user_public(user),
        "stats": [
            {"label": "Extensions", "value": 25, "icon": "call", "color": "#3B82F6"},
            {"label": "DID Numbers", "value": 12, "icon": "hash", "color": "#22C55E"},
            {"label": "IVR Menus", "value": 8, "icon": "layers", "color": "#A855F7"},
            {"label": "Active Plans", "value": 3, "icon": "receipt", "color": "#F59E0B"},
        ],
        "account": {
            "full_name": user.get("name", "John Doe"),
            "username": user.get("username", "johndoe"),
            "email": user.get("email", "john.doe@depthroute.com"),
            "phone": user.get("phone", "+1 (555) 123-4567"),
            "role": user.get("role", "Administrator"),
            "account_type": "Master Account",
            "status": "Active",
            "member_since": "May 15, 2024 10:30 AM",
            "last_login": "May 25, 2024 09:45 AM",
        },
    }


app.include_router(api_router)

app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()
