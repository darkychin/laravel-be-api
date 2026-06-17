---
name: laravel
description: Enforces Laravel 12 best practices, Pest PHP testing guidelines, and Laravel Pint styling conventions. Activate this skill when creating or editing controllers, models, migrations, service classes, jobs, routes, or Pest tests.
---

# Laravel 12 Agent Skill

This skill provides comprehensive instructions for building, refactoring, and maintaining applications in this codebase, which is built on Laravel 12, PHP 8.2+, Pest PHP, and Laravel Sail.

## Core Principles

- **Framework-Native First**: Always use Laravel's built-in features, helpers, and wrappers instead of native PHP equivalents or writing custom logic (e.g., `Http::get()` instead of `curl`, `File` facade instead of `file_get_contents`, `collect()` instead of array manipulation helper functions).
- **Strong Typing**: Use typed properties, method parameters, and explicit return types (including `: void` for methods returning nothing). Use constructor property promotion for dependency injection.
- **Strict Eloquent**: Eloquent models should not silently discard attributes. Ensure strict mode is handled or observed.
- **Coding Standard**: Follow Laravel Pint / PSR-12 coding standard. Use modern PHP 8.2+ syntax features (e.g., match expressions, readonly properties, nullsafe operator).

## Architecture & Code Organization

### 1. Controllers
- Keep controllers thin and focused on HTTP concerns (handling input, invoking business logic, returning responses).
- Use Single Action Controllers (`__invoke`) where appropriate.
- Never write business logic directly inside controllers; delegate to Services, Action classes, or Eloquent models.
- Always use **Form Requests** for validating request payloads instead of validating inline within the controller.

### 2. Models & Database
- Define type-hints or DocBlocks for Eloquent relations and custom attributes.
- Use native return types for relations (e.g., `public function posts(): HasMany`).
- Keep model schemas clean. Always write comprehensive, type-safe database migrations.
- When querying database/Eloquent, prefer Eloquent builders. Avoid raw SQL queries unless absolutely necessary for performance.

### 3. Services & Actions
- Extract business logic, complex integrations, or multi-step operations into dedicated `App\Services` or `App\Actions` classes.
- Inject dependencies through the constructor.
- Leverage Laravel's Service Container for auto-wiring and dependency injection.

### 4. Routing
- Group related routes using route group wrappers (`Route::middleware`, `Route::prefix`, `Route::name`).
- Use named routes for all routes (e.g., `->name('users.index')`).
- Route actions should point to class references (e.g., `[UserController::class, 'index']`) instead of string actions.

## Testing with Pest PHP

- All tests must be written using **Pest PHP** syntax (e.g., `it()`, `test()`, `describe()`).
- Leverage Pest's built-in Laravel plugins and assertions.
- Use HTTP/Feature tests to test controller endpoints and middleware.
- Use Unit tests for pure business logic, custom helpers, or service classes.
- Use **RefreshDatabase** or **DatabaseTransactions** traits when testing DB interactions.
- Avoid writing mock implementations manually if Laravel's Facade mocks (e.g., `Http::fake()`, `Queue::fake()`, `Event::fake()`) are available.

## Command & Code Execution

- When running Artisan commands or composer scripts in development:
  - Prefix with `./vendor/bin/sail` if running outside the container, or run directly within the container depending on environment.
- Run `composer test` or `./vendor/bin/pest` to verify changes do not break tests.
- Run `./vendor/bin/pint` to format files before finalizing changes.
