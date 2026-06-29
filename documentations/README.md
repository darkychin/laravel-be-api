---
last_updated: 2026-06-29
maintained_by: [Darky, ai-agent]
---

# Project Documentation

This directory contains feature-level documentation for the Laravel API Gateway project.

## Structure

Each feature has its own folder with consistent files:

| File | Purpose | Primary Author |
|---|---|---|
| `overview.md` | Feature intent, business rules, open questions | 👤 Human |
| `api.md` | Endpoint contracts, request/response schemas | 🤖 AI (human-reviewed) |
| `flows.md` | Sequence diagrams in Mermaid syntax | Both |
| `changelog.md` | Feature-level change history | 🤖 AI (human-reviewed) |

## Features

- [`auth/`](auth/) — User authentication (login, logout, token management)
- [`admin/`](admin/) — Admin user management and invitation system
- [`health/`](health/) — System health checks (DB, Redis, Storage)
- [`infrastructure/`](infrastructure/) — Docker, Nginx, and environment setup

## Conventions

### YAML Frontmatter

Every documentation file starts with YAML frontmatter containing parseable metadata:

```yaml
---
feature: auth               # feature identifier
status: implemented          # draft | in-progress | implemented | deprecated
owner: Darky                    # who to ask about this feature
related_models: [User]       # Eloquent models involved
tags: [sanctum, api]         # searchable tags
---
```

### Writing Guidelines

1. **Humans** write intent and business rules in `overview.md`
2. **AI agents** generate `api.md` from code and append to `changelog.md`
3. **Both** collaborate on `flows.md` — humans sketch, AI converts to Mermaid
4. Use `_templates/` as a starting point for new features
5. All changes are human-reviewed before merging
