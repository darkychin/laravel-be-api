---
name: doc-writer
description: Generates and updates feature documentation under documentations/. Activate when creating, updating, or generating docs for a feature — including overview, API reference, Mermaid flow diagrams, and changelog entries. Triggers on "document this feature", "generate docs", "update docs", "write documentation", or after implementing a new feature.
---

# Documentation Writer Skill

This skill generates structured feature documentation under `documentations/` that is parseable by both humans and AI agents.

## Documentation Structure

Every feature gets its own folder under `documentations/<feature-name>/` with up to 4 files:

| File | Purpose | Primary Author |
|---|---|---|
| `overview.md` | Feature intent, business rules, scope, open questions | 👤 Human (AI enriches) |
| `api.md` | Endpoint contracts, request/response schemas | 🤖 AI (from code) |
| `flows.md` | Sequence diagrams in Mermaid syntax | Both |
| `changelog.md` | Feature-level change history | 🤖 AI (appends) |

## Templates

Always use the templates in `documentations/_templates/` as your starting point:

- `feature-overview.template.md` — for `overview.md`
- `feature-api.template.md` — for `api.md`
- `feature-flows.template.md` — for `flows.md`

Read these templates before generating any documentation file.

## YAML Frontmatter (Required)

Every documentation file MUST start with YAML frontmatter. This is the contract between human and AI — it enables programmatic scanning without parsing prose.

### `overview.md` frontmatter

```yaml
---
feature: <feature-name>        # lowercase, hyphenated identifier
status: draft                  # draft | in-progress | implemented | deprecated
owner: <author>                # who to ask about this feature
related_models:                # Eloquent models involved
  - ModelName
tags: [tag1, tag2]             # searchable tags
---
```

### `api.md` frontmatter

```yaml
---
feature: <feature-name>
auto_generated: true           # true if AI generated this from code
last_synced: YYYY-MM-DD        # when this was last verified against code
---
```

### `flows.md` frontmatter

```yaml
---
feature: <feature-name>
---
```

## How to Generate Each File

### Step 1: Read Source Code

Before generating documentation, read the relevant source files:

1. **Route files**: `routes/api.php`, `routes/web.php` — identify endpoints, middleware, route names
2. **Controllers**: Find the controller classes referenced in routes — extract request handling logic
3. **Form Requests**: Check `app/Http/Requests/` — extract validation rules for request fields
4. **Models**: Check `app/Models/` — identify related models, relationships, and traits
5. **Existing docs**: Check if `documentations/<feature>/` already exists — update rather than overwrite

### Step 2: Generate `overview.md`

- Start from `documentations/_templates/feature-overview.template.md`
- Fill in the YAML frontmatter with actual values from code analysis
- Write the **Purpose** section describing what the feature does and why
- List **Business Rules** extracted from controller logic, middleware, and validation
- Define **Scope** (in-scope vs out-of-scope) based on what's implemented vs what's not
- Add **Open Questions** for any ambiguities discovered during code reading

### Step 3: Generate `api.md`

- Start from `documentations/_templates/feature-api.template.md`
- Set `auto_generated: true` and `last_synced` to today's date
- For each endpoint, document:
  - HTTP method and path
  - Route name (from `->name()`)
  - Middleware (from `->middleware()`)
  - Form Request class (if used)
  - Request fields as a table (from Form Request `rules()` method)
  - Success response with example JSON (from controller return statements)
  - Error responses table with status codes, conditions, and response bodies
- Separate endpoints with `---` horizontal rules

### Step 4: Generate `flows.md`

- Start from `documentations/_templates/feature-flows.template.md`
- Create **Mermaid sequence diagrams** for each key workflow
- Use `sequenceDiagram` for request/response flows
- Include `alt/else/end` blocks for branching logic (error cases, auth checks)
- Use `Note over` for step annotations in multi-step flows
- Standard participants: `Client`, `Server`, `DB` (add others as needed like `Redis`, `Storage`)

### Step 5: Generate or Append to `changelog.md`

- If the file doesn't exist, create it with the initial entry
- If it already exists, **prepend** the new entry at the top (newest first)
- Each entry format:

```markdown
## [YYYY-MM-DD] Short description

- **Author**: AI (agent) | <username> (human)
- What was changed (bullet points)
```

## Rules

1. **Never overwrite `overview.md` without asking** — this is human-owned content. You may enrich it (add code links, fill gaps) but always ask before rewriting business rules.
2. **Always overwrite `api.md` on regeneration** — this is code-derived and should reflect current state. Update `last_synced` date.
3. **Append to `changelog.md`** — never delete existing entries.
4. **Use Mermaid for all diagrams** — no ASCII art, no text-based flow descriptions.
5. **Match existing style** — read at least one existing feature's docs (e.g., `documentations/auth/`) before generating new ones to match tone and formatting.
6. **Update `documentations/README.md`** — when creating a new feature folder, add it to the Features list in the README.

## When to Trigger

- **After implementing a new feature**: Generate all 4 files
- **After modifying endpoints**: Regenerate `api.md` and append to `changelog.md`
- **After changing business logic**: Update `flows.md` and append to `changelog.md`
- **On explicit request**: "document this", "generate docs", "update documentation"

## Example: Generating Docs for a New Feature

If asked to document a feature called "notifications":

1. Read routes, controllers, form requests, and models related to notifications
2. Create `documentations/notifications/overview.md` from template + code analysis
3. Create `documentations/notifications/api.md` from route/controller definitions
4. Create `documentations/notifications/flows.md` with Mermaid sequence diagrams
5. Create `documentations/notifications/changelog.md` with initial entry
6. Update `documentations/README.md` to include the new feature link
