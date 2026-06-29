---
feature: auth
---

# Auth Flows

## 1. Login

User authenticates with email and password, receives a Sanctum bearer token.

```mermaid
sequenceDiagram
    participant Client
    participant Server
    participant DB

    Client->>Server: POST /api/login {email, password}
    Server->>DB: Verify credentials
    alt Valid credentials
        DB-->>Server: User record
        Server->>Server: Generate Sanctum token
        Server-->>Client: 200 {token, user}
    else Invalid credentials
        Server-->>Client: 401 {message}
    end
```

## 2. Logout

User sends logout request, server revokes the Sanctum token.

```mermaid
sequenceDiagram
    participant Client
    participant Server

    Client->>Server: POST /api/logout (Bearer token)
    Server->>Server: Revoke Sanctum token
    Server-->>Client: 200 {message: "Logged out"}
```

## 3. Invitation-Based Password Setup

New users receive a signed invitation URL from an admin. They verify the URL, then set their password.

```mermaid
sequenceDiagram
    participant Client
    participant Server
    participant DB

    Note over Client,Server: Step 1 - Verify invitation
    Client->>Server: GET /api/invitations/verify?email=...&signature=...
    Server->>Server: Validate signed URL
    alt Invalid or expired
        Server-->>Client: 403 {message}
    else Already used
        Server-->>Client: 403 "Invitation already used"
    else Valid
        Server-->>Client: 200 {email}
    end

    Note over Client,Server: Step 2 - Set password
    Client->>Server: POST /api/invitations/set-password
    Server->>Server: Re-validate signature
    Server->>DB: Update password + set email_verified_at
    Server-->>Client: 200 {message: "Password has been set successfully."}
```

## 4. Password Reset

Follows the same signed-URL pattern as invitations. Admin generates a new signed URL for the user.

```mermaid
sequenceDiagram
    participant Client
    participant Server
    participant DB

    Client->>Server: Send reset password credentials
    Server->>Server: Validate credentials
    alt Validation failed
        Server--xClient: 503 Error
    else Valid
        Client->>Server: Submit new password & confirmation
        Server->>Server: Validate and sanitize passwords
        Server->>DB: Update password
        Server-->>Client: Login successful with new password
    end
```
