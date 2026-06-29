---
feature: admin
status: implemented
owner: Darky
related_models:
  - User
tags: [admin, user-management, invitations]
---

# Admin Management

## Purpose

Allow administrators to manage users — creating new accounts via invitation, resending invitation links, editing existing users' details, deleting user accounts, and viewing a list of all current users.

## Business Rules

- Only users with the `admin` ability can access admin endpoints (enforced via `can:admin` gate).
- Admin must be authenticated via Sanctum token before accessing admin features.
- When an admin creates a new user, a random 32-character password is generated (user must set their own via invitation URL).
- Invitation URLs are signed URLs containing the user's email.
- Resending an invitation is only allowed if the user has **not** yet verified their email (`email_verified_at IS NULL`).
- Admin can modify user name and email.
- Admins cannot delete their own accounts.
- User deletion uses **Soft Deletes** (`deleted_at` timestamp), preserving the record for historical audit integrity.
- A database-level partial unique index (`users_email_unique WHERE deleted_at IS NULL`) enables reusing emails of soft-deleted users.

## Scope

### In Scope

- Listing all user accounts (including the admin themselves)
- Soft deleting user accounts (preventing self-deletion, freeing up emails)
- Updating existing user details (name and email)
- User creation with invitation URL generation
- Resending invitations for unactivated users
- Admin dashboard access

### Out of Scope

- Role/permission management beyond the `admin` gate
- Bulk user operations

## Open Questions

- [ ] Should there be an audit log for admin actions?
