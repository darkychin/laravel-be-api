---
feature: auth
auto_generated: true
last_synced: 2026-06-29
---

# Auth API Reference

## `GET` `/api/user`

**Route Name**: `user`
**Middleware**: `auth:sanctum`

Returns the currently authenticated user.

### Request

No request body. Requires `Authorization: Bearer {token}` header.

### Success Response (`200`)

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": "2026-06-29T00:00:00.000000Z",
  "created_at": "2026-06-29T00:00:00.000000Z",
  "updated_at": "2026-06-29T00:00:00.000000Z"
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Missing or invalid token | `{"message": "Unauthenticated."}` |

---

## `GET` `/api/invitations/verify`

**Route Name**: `invitations.verify`
**Middleware**: None (public, but signature-validated)

Verifies that an invitation URL has a valid signature and the user hasn't already activated.

### Request (Query Parameters)

| Field | Type | Required | Description |
|---|---|---|---|
| email | string | yes | The invited user's email address |
| expires | numeric | yes | URL expiration timestamp |
| signature | string | yes | Signed URL signature |

### Success Response (`200`)

```json
{
  "message": "Invitation is valid.",
  "email": "invited@example.com"
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 400 | Missing email parameter | `{"message": "Email parameter is missing."}` |
| 403 | Invalid/expired signature | `{"message": "Invalid or expired signature."}` |
| 403 | Invitation already used | `{"message": "Invitation already used."}` |
| 404 | User not found | `{"message": "User not found."}` |

---

## `POST` `/api/invitations/set-password`

**Route Name**: `invitations.set-password`
**Middleware**: None (public, but signature-validated)

Sets the user's password and marks their email as verified.

### Request

| Field | Type | Required | Description |
|---|---|---|---|
| email | string | yes | The invited user's email (must exist in `users` table) |
| password | string | yes | New password (must meet default password rules) |
| password_confirmation | string | yes | Password confirmation |
| signature | string | yes | Signed URL signature |
| expires | numeric | yes | URL expiration timestamp |

### Success Response (`200`)

```json
{
  "message": "Password has been set successfully."
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 403 | Invalid/expired signature | `{"message": "Invalid or expired signature."}` |
| 403 | Invitation already used | `{"message": "Invitation already used."}` |
| 422 | Validation failed | `{"message": "...", "errors": {...}}` |
