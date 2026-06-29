---
feature: infrastructure
status: implemented
owner: Darky
tags: [docker, nginx, postgres, redis, devops]
---

# Infrastructure

## Purpose

Defines the containerized development environment and deployment configuration for the Laravel API Gateway.

## Architecture

The application runs as a multi-container Docker stack:

| Container | Role | Image/Base |
|---|---|---|
| `web` | Nginx reverse proxy, serves static assets | Nginx |
| `php-fpm` | PHP request processor | PHP 8.2+ FPM |
| `postgres` | Primary database | PostgreSQL |
| `workspace` | CLI environment (Artisan, Composer, NPM) | Custom |
| `redis` | Cache, sessions, and queue broker | Redis |

## Key Files

| File | Purpose |
|---|---|
| `compose.dev.yaml` | Development Docker Compose configuration |
| `compose.prod.yaml` | Production Docker Compose configuration |
| `docker/` | Dockerfiles and service-specific configuration |
| `.env` | Environment variables (not committed) |
| `.env.example` | Template for environment variables |

## Conventions

- **No local PHP**: All PHP/Artisan/Composer commands run inside the `workspace` container.
- **Command pattern**: `docker compose -f compose.dev.yaml exec -T workspace [command]`
- **Testing**: Uses Pest PHP with in-memory SQLite for isolation.
- **Code style**: Enforced via Laravel Pint.
