---
feature: auth
status: implemented
owner: Darky
related_models:
  - User
  - PersonalAccessToken
tags: [sanctum, api, tokens, invitation]
---

# Authentication

## Purpose

Allow users to authenticate via API tokens using Laravel Sanctum. This feature also handles the invitation-based onboarding flow where admins create users and those users set their passwords via signed invitation URLs.

## Business Rules

- A user receives a Sanctum bearer token upon successful login.
- Tokens can be revoked on logout.
- New users are created by admins with a random password — they must set their own password via a signed invitation URL.
- Invitation URLs are time-limited signed URLs containing the user's email.
- A user can only use an invitation link once (checked via `email_verified_at`).
- Password reset follows the same signed-URL pattern as invitations.

## Scope

### In Scope

- Token-based login/logout via Sanctum
- Invitation URL verification
- Password setting via signed URL
- Authenticated user retrieval (`/api/user`)

### Out of Scope

- OAuth / social login
- Session-based (cookie) authentication
- Two-factor authentication

## Open Questions

- [ ] Should tokens have expiration times?
- [ ] Rate limiting on login attempts?
- [ ] Should invitation URLs have a configurable expiry duration?
