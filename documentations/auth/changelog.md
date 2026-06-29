# Auth Changelog

## [2026-06-29] Replace user route closure with controller

- **Author**: AI (agent)
- Moved inline closure logic from `routes/api.php` to a new single-action `AuthenticatedUserController`

## [2026-06-29] Documentation restructured

- **Author**: AI (agent)
- Migrated informal flow sketches from `documentation-bk/auth/user.md`
- Converted text-based flows to Mermaid sequence diagrams
- Generated `api.md` from route definitions and controller code
- Created `overview.md` with business rules and open questions

## [Undated] Initial feature implementation

- **Author**: Darky (human)
- Implemented Sanctum token authentication on `GET /api/user`
- Created invitation verification flow (`GET /api/invitations/verify`)
- Created password setting flow (`POST /api/invitations/set-password`)
