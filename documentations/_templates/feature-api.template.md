---
feature: <feature-name>
auto_generated: false
last_synced: YYYY-MM-DD
---

# <Feature Name> API Reference

## `METHOD` `/api/endpoint`

**Middleware**: `auth:sanctum`

### Request

| Field | Type | Required | Description |
|---|---|---|---|
| field_name | string | yes | Description |

### Success Response (`200`)

```json
{
  "key": "value"
}
```

### Error Responses

| Status | Condition | Body |
|---|---|---|
| 401 | Unauthenticated | `{"message": "Unauthenticated."}` |
| 422 | Validation failed | `{"message": "...", "errors": {...}}` |
