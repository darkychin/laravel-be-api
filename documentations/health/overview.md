---
feature: health
status: implemented
owner: Darky
related_models: []
tags: [monitoring, diagnostics, devops]
---

# System Health Checks

## Purpose

Provide a single endpoint that verifies the operational status of all critical infrastructure services — database, cache, and file storage. Used for monitoring, load balancer health probes, and deployment verification.

## Business Rules

- The health endpoint checks three services: PostgreSQL (via `SELECT 1`), Redis (via write/read test), and filesystem storage (via file write/read/delete).
- Returns `HTTP 200` only if **all** service checks pass.
- Returns `HTTP 503` if **any** service check fails.
- Each service status is reported individually in the JSON response.
- The endpoint is publicly accessible (no authentication required).

## Scope

### In Scope

- Database connectivity check
- Redis cache read/write check
- Storage filesystem permissions check
- Aggregated health status response

### Out of Scope

- Application-level health (queue depth, job failures)
- External service health (third-party APIs)
- Detailed error messages in production

## Open Questions

- [ ] Should health check responses be cached to avoid hammering services?
- [ ] Should the endpoint be rate-limited?
- [ ] Add queue health check (dispatch + process a test job)?
