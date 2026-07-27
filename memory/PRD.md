# Depth Route Dialer — PRD

## Overview
Mobile VoIP/SIP business phone app (React Native / Expo). Dark-themed UI matching provided mockups. All calling / SIP behavior is **simulated** (UI-only) and works fully in Expo Go.

## Auth
- **Email/password (JWT):** seeded demo user `demo@depthroute.com` / `demo1234`.
- **Emergent Google Login:** platform-aware redirect + `session_id` exchange at `/api/auth/google/session`.
- Sessions stored in MongoDB (`user_sessions`), tokens via `expo-secure-store`.

## Navigation
- Root `Stack`: `index` → `login` | `(tabs)` | 13 detail screens.
- Bottom tabs: **Dashboard, Dialer, Contacts, Call Logs, More**.
- **Sidebar drawer** accessible via hamburger on every screen. Full menu grouped as MAIN / MANAGE / SUPPORT.
- **More tab** shows every screen grouped as COMMUNICATION / MANAGE / ACCOUNT + Profile card + Logout.

## Screens (18 total)
Dashboard, Dialer, Contacts, Call Logs, Voicemails, SMS, Recordings, SIP Accounts, Extensions, Numbers, IVR, Plans, Billing, Reports, User Profile, Notifications, Help & Support, Login.

## Backend (`/api`)
FastAPI + MongoDB. Endpoints: `auth/*`, `dashboard`, `contacts`, `call-logs`, `voicemails`, `sms`, `recordings`, `sip-accounts`, `extensions`, `numbers`, `ivr`, `plans`, `billing`, `reports`, `notifications`, `support`, `profile`. All seeded with mock data matching the mockups.

## Design tokens
Deep navy `#050B1A` background, `#0F1A30` cards, `#2F80ED` primary, green/red/purple/orange/yellow/teal accents.

## Non-goals for MVP
- Real SIP calling (would need a native dev build + SIP library like linphone-sdk).
- Real payments / billing writes.
- Editing / creating extensions, IVRs, SIP accounts (list-view only).
