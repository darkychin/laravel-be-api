---
feature: admin
auto_generated: true
last_synced: 2026-06-29
---

# Admin API Reference

## `GET` `/api/admin/dashboard`

**Route Name**: `admin.dashboard`
**Middleware**: `auth:sanctum`, `can:admin`

Returns a welcome message for the admin dashboard.

### Request

No request body. Requires `Authorization: Bearer {token}` header.

### Success Response (`200`)

```json
{
  "message": "Welcome to the admin dashboard."
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Missing or invalid token | `{"message": "Unauthenticated."}` |
| 403 | User lacks admin ability | `{"message": "This action is unauthorized."}` |

---

## `GET` `/api/admin/users`

**Route Name**: `admin.users.index`
**Middleware**: `auth:sanctum`, `can:admin`

Returns a list of all current users registered in the database, ordered by ID.

### Request

No request body. Requires `Authorization: Bearer {token}` header.

### Success Response (`200`)

```json
{
  "users": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "is_admin": true,
      "email_verified_at": "2026-06-29T00:00:00.000000Z",
      "created_at": "2026-06-29T00:00:00.000000Z",
      "updated_at": "2026-06-29T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "is_admin": false,
      "email_verified_at": null,
      "created_at": "2026-06-29T00:00:00.000000Z",
      "updated_at": "2026-06-29T00:00:00.000000Z"
    }
  ]
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Missing or invalid token | `{"message": "Unauthenticated."}` |
| 403 | User lacks admin ability | `{"message": "This action is unauthorized."}` |

---

## `POST` `/api/admin/users`

**Route Name**: `admin.users.store`
**Middleware**: `auth:sanctum`, `can:admin`
**Form Request**: `AdminStoreUserRequest`

Creates a new user with a random password and returns a signed invitation URL.

### Request

| Field | Type | Required | Validation |
|---|---|---|---|
| name | string | yes | max:255 |
| email | string | yes | valid email, max:255, unique among active users (`deleted_at IS NULL`) |

### Success Response (`201`)

```json
{
  "invitation_url": "https://example.com/api/invitations/verify?email=...&expires=...&signature=...",
  "user": {
    "id": 2,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "created_at": "2026-06-29T00:00:00.000000Z",
    "updated_at": "2026-06-29T00:00:00.000000Z"
  }
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Unauthenticated | `{"message": "Unauthenticated."}` |
| 403 | Not an admin | `{"message": "This action is unauthorized."}` |
| 422 | Validation failed | `{"message": "...", "errors": {"email": ["..."]}}` |

---

## `POST` `/api/admin/users/{user}/resend-invitation`

**Route Name**: `admin.users.resend`
**Middleware**: `auth:sanctum`, `can:admin`

Generates a new signed invitation URL for a user who hasn't yet set their password.

### Request

No request body. The `{user}` route parameter is the user's ID.
Requires `Authorization: Bearer {token}` header.

### Success Response (`200`)

```json
{
  "invitation_url": "https://example.com/api/invitations/verify?email=...&expires=...&signature=...",
  "message": "New invitation URL generated successfully."
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 400 | User already active | `{"message": "User already active."}` |
| 401 | Unauthenticated | `{"message": "Unauthenticated."}` |
| 403 | Not an admin | `{"message": "This action is unauthorized."}` |
| 404 | User not found | `{"message": "Not found."}` |

---

## `PUT` `/api/admin/users/{user}`

**Route Name**: `admin.users.update`
**Middleware**: `auth:sanctum`, `can:admin`
**Form Request**: `AdminUpdateUserRequest`

Updates the specified user's name and email.

### Request

| Field | Type | Required | Validation |
|---|---|---|---|
| name | string | yes | max:255 |
| email | string | yes | valid email, max:255, unique among active users (`deleted_at IS NULL`, ignores current user) |

### Success Response (`200`)

```json
{
  "message": "User updated successfully.",
  "user": {
    "id": 2,
    "name": "Jane Updated",
    "email": "jane.updated@example.com",
    "created_at": "2026-06-29T00:00:00.000000Z",
    "updated_at": "2026-06-29T00:00:00.000000Z"
  }
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Unauthenticated | `{"message": "Unauthenticated."}` |
| 403 | Not an admin | `{"message": "This action is unauthorized."}` |
| 404 | User not found | `{"message": "Not found."}` |
| 422 | Validation failed | `{"message": "...", "errors": {"email": ["..."]}}` |

---

## `DELETE` `/api/admin/users/{user}`

**Route Name**: `admin.users.destroy`
**Middleware**: `auth:sanctum`, `can:admin`

Soft deletes the specified user account. The record is preserved in the database with a `deleted_at` timestamp. An admin cannot delete their own account.

### Request

No request body. The `{user}` route parameter is the user's ID.
Requires `Authorization: Bearer {token}` header.

### Success Response (`200`)

```json
{
  "message": "User deleted successfully."
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 400 | Attempting to delete own account | `{"message": "You cannot delete your own account."}` |
| 401 | Unauthenticated | `{"message": "Unauthenticated."}` |
| 403 | Not an admin | `{"message": "This action is unauthorized."}` |
| 404 | User not found | `{"message": "Not found."}` |
