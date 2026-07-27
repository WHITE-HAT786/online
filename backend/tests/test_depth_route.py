"""Depth Route Dialer - Backend API tests."""
import os
import uuid
import pytest
import requests

BASE_URL = os.environ.get("EXPO_PUBLIC_BACKEND_URL", "https://mobile-design-dev.preview.emergentagent.com").rstrip("/")
API = f"{BASE_URL}/api"

DEMO_EMAIL = "demo@depthroute.com"
DEMO_PASSWORD = "demo1234"


@pytest.fixture(scope="session")
def client():
    s = requests.Session()
    s.headers.update({"Content-Type": "application/json"})
    return s


@pytest.fixture(scope="session")
def token(client):
    r = client.post(f"{API}/auth/login", json={"email": DEMO_EMAIL, "password": DEMO_PASSWORD})
    assert r.status_code == 200, f"Login failed: {r.status_code} {r.text}"
    data = r.json()
    assert "token" in data and "user" in data
    return data["token"]


@pytest.fixture(scope="session")
def auth_headers(token):
    return {"Authorization": f"Bearer {token}", "Content-Type": "application/json"}


# ---- Auth ----
class TestAuth:
    def test_login_success(self, client):
        r = client.post(f"{API}/auth/login", json={"email": DEMO_EMAIL, "password": DEMO_PASSWORD})
        assert r.status_code == 200
        data = r.json()
        assert data["user"]["email"] == DEMO_EMAIL
        assert data["user"]["user_id"] == "user_demo_001"
        assert isinstance(data["token"], str) and len(data["token"]) > 20

    def test_login_wrong_password(self, client):
        r = client.post(f"{API}/auth/login", json={"email": DEMO_EMAIL, "password": "wrong-pass"})
        assert r.status_code == 401

    def test_login_nonexistent_user(self, client):
        r = client.post(f"{API}/auth/login", json={"email": "nope@nope.com", "password": "x"})
        assert r.status_code == 401

    def test_register_success(self, client):
        email = f"TEST_{uuid.uuid4().hex[:8]}@example.com"
        r = client.post(f"{API}/auth/register", json={"name": "Test User", "email": email, "password": "pw12345"})
        assert r.status_code == 200, r.text
        data = r.json()
        assert data["user"]["email"] == email
        assert "token" in data
        # verify /me works with the new token
        me = client.get(f"{API}/auth/me", headers={"Authorization": f"Bearer {data['token']}"})
        assert me.status_code == 200
        assert me.json()["email"] == email

    def test_register_duplicate(self, client):
        r = client.post(f"{API}/auth/register", json={"name": "X", "email": DEMO_EMAIL, "password": "x12345"})
        assert r.status_code == 400

    def test_me_with_token(self, client, auth_headers):
        r = client.get(f"{API}/auth/me", headers=auth_headers)
        assert r.status_code == 200
        data = r.json()
        assert data["email"] == DEMO_EMAIL
        assert data["user_id"] == "user_demo_001"
        assert data["role"] == "Administrator"

    def test_me_without_token(self, client):
        r = client.get(f"{API}/auth/me")
        assert r.status_code == 401

    def test_me_bad_token(self, client):
        r = client.get(f"{API}/auth/me", headers={"Authorization": "Bearer invalid.token.xxx"})
        assert r.status_code == 401

    def test_logout(self, client, auth_headers):
        r = client.post(f"{API}/auth/logout", headers=auth_headers)
        assert r.status_code == 200
        assert r.json().get("ok") is True


# ---- Mock data endpoints ----
ENDPOINT_SPECS = [
    ("/dashboard", ["profile", "stats", "recent_calls", "quick_stats"]),
    ("/contacts", ["stats", "items"]),
    ("/call-logs", ["stats", "items"]),
    ("/voicemails", ["stats", "storage", "items"]),
    ("/sms", ["stats", "recent"]),
    ("/recordings", ["stats", "storage", "items"]),
    ("/sip-accounts", ["stats", "items"]),
    ("/extensions", ["stats", "items"]),
    ("/numbers", ["stats", "items"]),
    ("/ivr", ["stats", "items"]),
    ("/plans", ["stats", "items"]),
    ("/billing", ["stats", "invoices", "summary", "payment_methods"]),
    ("/reports", ["stats", "call_activity", "direction", "destinations", "trunks"]),
    ("/notifications", ["items"]),
    ("/support", ["topics", "tickets"]),
    ("/profile", ["user", "stats", "account"]),
]


@pytest.mark.parametrize("path,keys", ENDPOINT_SPECS)
class TestMockEndpoints:
    def test_requires_auth(self, client, path, keys):
        r = client.get(f"{API}{path}")
        assert r.status_code == 401, f"{path} should require auth, got {r.status_code}"

    def test_returns_expected_shape(self, client, auth_headers, path, keys):
        r = client.get(f"{API}{path}", headers=auth_headers)
        assert r.status_code == 200, f"{path} -> {r.status_code} {r.text[:200]}"
        data = r.json()
        for k in keys:
            assert k in data, f"{path}: missing key '{k}'"
        # ensure any *items* / *invoices* / *recent* list is non-empty
        for k in keys:
            if isinstance(data[k], list):
                assert len(data[k]) > 0, f"{path}: list '{k}' is empty"
