# Project Background & Agent Context

This document establishes the business context, domain details, technical architecture, and development boundaries for all AI coding assistants working in this repository.

---

## 📖 Project Overview

This is pure backend Laravel project that serve as a API gateway that manages user login.

- **Application Purpose**: To understand Laravel and how it handle authentication.
- **Target Audience**: developers
- **Current Phase**: Initial prototype

---

## 🏗️ Architecture & Tech Stack

This project is built using the following development stack:

- **Backend Framework**: Laravel 12.x running on PHP 8.2+.
- **Database**: PostgreSQL (Development & Production).
- **Cache & Queues**: Redis (handles session state, caching, and background jobs).
- **Web Server**: Nginx.
- **Docker Orchestration**: The local development environment runs in a multi-container stack defined in [compose.dev.yaml](compose.dev.yaml):
  1. `web` (Nginx serving assets and handling HTTP requests)
  2. `php-fpm` (PHP request processor)
  3. `postgres` (Main database instance)
  4. `workspace` (CLI/command environment containing Node/NPM/Composer)
  5. `redis` (Cache & Session store)

---

## 🗄️ Domain Models & Data Structures

*Use this section to outline your main database entities and their business meaning so the agent designs relations correctly.*

- **User**: Standard user accounts representing authentication and access rights, using Sanctum's `HasApiTokens` to generate and manage secure tokens.
- **PersonalAccessToken**: Laravel Sanctum's default token model for validating client API requests.

---

## 🔄 Core Workflows

1. **System Health Check Workflow (`/health`)**:
   - Checks the health of the PostgreSQL database (`SELECT 1`), the Redis Cache store (write/read test), and filesystem storage permissions (file write/read/delete check).
   - Returns a JSON status payload with `HTTP 200` on success or `HTTP 503` if any service check fails.
2. **Request Logging Flow (`/` and `/info`)**:
   - Resolves routing for base views and diagnostic screens (`phpinfo()`) while writing logs (`Log::info`) to verify storage and file logging functionality.
3. **Sanctum API Authentication Flow (`/api/user`)**:
   - Implements Laravel Sanctum token-based authentication.
   - Endpoint `/api/user` is protected by `auth:sanctum` middleware, requiring a valid bearer token to return authenticated user JSON data.


---

## 🛡️ Technical Guardrails & Command Execution

AI agents must strictly adhere to the following execution constraints:

### 1. Command Context
- **No Local Host PHP**: PHP is not installed on the local host machine. 
- **Executing CLI Commands**: All Artisan, Composer, or NPM commands must be executed inside the running `workspace` container:
  ```bash
  docker compose -f compose.dev.yaml exec -T workspace [command]
  ```

### 2. Testing & Quality
- **Testing Framework**: We use **Pest PHP**. Do not write PHPUnit tests.
- **Test Database**: Tests are configured in [phpunit.xml](phpunit.xml) to run in isolation using an in-memory SQLite database (`DB_DATABASE=:memory:`).
- **Verification**: Run the test suite before submitting or declaring a task complete:
  ```bash
  docker compose -f compose.dev.yaml exec -T workspace ./vendor/bin/pest
  ```
- **Code Style**: Run Laravel Pint to format code according to project style standards:
  ```bash
  docker compose -f compose.dev.yaml exec -T workspace ./vendor/bin/pint
  ```
