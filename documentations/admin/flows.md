---
feature: admin
---

# Admin Flows

## 1. Admin Verification

Before performing admin actions, the system verifies the current user has admin privileges.

```mermaid
sequenceDiagram
    participant Client
    participant Server

    Client->>Server: Request with Bearer token
    Server->>Server: Verify auth:sanctum
    Server->>Server: Check can:admin gate
    alt Is not admin
        Server-->>Client: 403 Forbidden
    else Is admin
        Server-->>Client: 200 (proceed with action)
    end
```

## 2. Create User & Generate Invitation

Admin creates a new user, system generates a signed invitation URL for the user to set their password.

```mermaid
sequenceDiagram
    participant Admin
    participant Server
    participant DB

    Admin->>Server: POST /api/admin/users {name, email}
    Server->>Server: Validate request (AdminStoreUserRequest)
    Server->>DB: Create user with random password
    Server->>Server: Generate signed invitation URL
    Server-->>Admin: 201 {invitation_url, user}
```

## 3. Resend Invitation

Admin resends an invitation URL for a user who hasn't activated yet. Can also be used for password reset flows.

```mermaid
sequenceDiagram
    participant Admin
    participant Server
    participant DB

    Admin->>Server: POST /api/admin/users/{id}/resend-invitation
    Server->>DB: Find user by ID
    alt User already verified
        Server-->>Admin: 400 "User already active."
    else Not yet verified
        Server->>Server: Generate new signed invitation URL
        Server-->>Admin: 200 {invitation_url, message}
    end
```

## 4. Modify User

Admin updates a user's name or email.

```mermaid
sequenceDiagram
    participant Admin
    participant Server
    participant DB

    Admin->>Server: PUT /api/admin/users/{user} {name, email}
    Server->>Server: Validate request (AdminUpdateUserRequest) & authorize
    Server->>DB: Update user record
    Server-->>Admin: 200 {message, user}
```

## 5. Delete User

Admin deletes a user account, preventing self-deletion.

```mermaid
sequenceDiagram
    participant Admin
    participant Server
    participant DB

    Admin->>Server: DELETE /api/admin/users/{user}
    Server->>Server: Validate token and authorize (can:admin)
    alt Attempting to delete own account
        Server-->>Admin: 400 "You cannot delete your own account."
    else Deleting another user
        Server->>DB: Delete user record
        Server-->>Admin: 200 {message}
    end
```

## 6. List Users

Admin views a list of all current users, including themselves.

```mermaid
sequenceDiagram
    participant Admin
    participant Server
    participant DB

    Admin->>Server: GET /api/admin/users
    Server->>Server: Validate token and authorize (can:admin)
    Server->>DB: Fetch all users ordered by ID
    Server-->>Admin: 200 {users}
```
