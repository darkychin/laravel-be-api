# Admin Changelog

## [2026-06-29] Switch user deletion to Soft Deletes

- **Author**: AI (agent)
- Added `SoftDeletes` trait to `User` model
- Modified `0001_01_01_000000_create_users_table.php` migration to define `deleted_at` column and a PostgreSQL/SQLite-compatible partial unique index (`users_email_unique` only when `deleted_at IS NULL`)
- Updated `AdminStoreUserRequest` and `AdminUpdateUserRequest` unique validation rules to ignore soft-deleted users
- Updated `AdminDeleteUserTest` to assert soft deletion and verified that emails of soft-deleted users can be reused for new invitations

## [2026-06-29] Implement user listing capability for admins

- **Author**: AI (agent)
- Added `GET /api/admin/users` endpoint
- Added `index` method to `UserController`
- Created `AdminListUsersTest` feature test class

## [2026-06-29] Implement user deletion capability for admins

- **Author**: AI (agent)
- Added `DELETE /api/admin/users/{user}` endpoint
- Added `destroy` method to `UserController` preventing self-deletion
- Created `AdminDeleteUserTest` feature test class

## [2026-06-29] Implement user update capability for admins

- **Author**: AI (agent)
- Added `PUT /api/admin/users/{user}` endpoint
- Added `AdminUpdateUserRequest` validation request class
- Added `update` method to `UserController`
- Created `AdminUpdateUserTest` test class

## [2026-06-29] Replace dashboard route closure with controller

- **Author**: AI (agent)
- Moved inline closure logic from `routes/api.php` to a new single-action `DashboardController`

## [2026-06-29] Documentation restructured

- **Author**: AI (agent)
- Migrated informal flow sketches from `documentation-bk/admin.md`
- Converted text-based flows to Mermaid sequence diagrams
- Generated `api.md` from route definitions and controller code
- Created `overview.md` with business rules and open questions

## [Undated] Initial feature implementation

- **Author**: Darky (human)
- Implemented admin dashboard endpoint (`GET /api/admin/dashboard`)
- Created user store endpoint with invitation URL generation (`POST /api/admin/users`)
- Created resend invitation endpoint (`POST /api/admin/users/{user}/resend-invitation`)
