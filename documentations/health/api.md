---
feature: health
auto_generated: true
last_synced: 2026-06-29
---

# Health API Reference

## `GET` `/health`

**Route Type**: Web (not API-prefixed)
**Middleware**: None (publicly accessible)

Checks the health of all critical infrastructure services.

### Request

No request body or authentication required.

### Success Response (`200`)

All services operational:

```json
{
  "database": "OK",
  "redis": "OK",
  "storage": "OK"
}
```

### Degraded Response (`503`)

One or more services failing:

```json
{
  "database": "OK",
  "redis": "Error",
  "storage": "OK"
}
```

### Service Checks

| Service | Check Method | Pass Condition |
|---|---|---|
| `database` | `DB::select('SELECT 1')` | Query executes without exception |
| `redis` | `Cache::store('redis')->put()` + `get()` | Written value matches read value |
| `storage` | `Storage::put()` + `get()` + `delete()` | Written content matches read content |
