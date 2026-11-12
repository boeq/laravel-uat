# UAT API reference

All paths are relative to `UAT_API_URL`. Every request needs `Authorization: Bearer $UAT_API_TOKEN`. Requests and
responses are JSON. `GET` needs a token that may read; every other method needs a token that may write.

## Configuration

`GET /configuration`

```json
{ "data": { "locales": ["en", "nl"], "default_locale": "en" } }
```

## Scenarios

### The scenario object

```json
{
    "id": 1,
    "status": "draft",
    "start_url": "/orders",
    "ready_for_retest_at": null,
    "translations": {
        "en": { "title": "Create an order", "description": "…", "preconditions": "…", "start_url": null }
    },
    "steps": [
        {
            "id": 1,
            "position": 1,
            "url": "/orders/create",
            "translations": {
                "en": { "title": "…", "instruction": "…", "expected_result": "…", "link_label": null, "url": null }
            }
        }
    ],
    "results": {
        "sessions_count": 0,
        "completed_sessions_count": 0,
        "accepted_steps_count": 0,
        "feedback_steps_count": 0,
        "open_feedback_count": 0
    },
    "created_at": "2026-11-12T10:00:00.000000Z",
    "updated_at": "2026-11-12T10:00:00.000000Z"
}
```

- `status` is `draft`, `active` or `archived`. Only active scenarios appear in the bubble.
- A `url` or `start_url` inside a translation overrides the shared one for that language.

### List, view, create, update, delete

| Request | Result |
|---|---|
| `GET /scenarios` | `{ "data": [scenario, …] }`. Filter with `?status=active`. |
| `GET /scenarios/{id}` | `{ "data": scenario }` |
| `POST /scenarios` | 201 with `{ "data": scenario }`. The scenario starts as a draft. |
| `PUT /scenarios/{id}` | `{ "data": scenario }` |
| `DELETE /scenarios/{id}` | 204. 409 when the scenario has sessions. |

Body for `POST` and `PUT`:

```json
{
    "start_url": "/orders",
    "translations": {
        "en": { "title": "Create an order", "description": "…", "preconditions": "…" }
    },
    "steps": [
        {
            "url": "/orders/create",
            "translations": {
                "en": {
                    "title": "Open the order form",
                    "instruction": "Click 'New order'.",
                    "expected_result": "An empty order form opens.",
                    "link_label": "Go to the orders"
                }
            }
        }
    ]
}
```

- `translations` and `steps` are required keys. They may be empty on a draft.
- Per language, a scenario needs `title` and `description`; a step needs `title`, `instruction` and `expected_result`.
- Language keys must be listed in `GET /configuration`.
- `PUT` replaces the scenario. Send `id` on every step you keep; a step without `id` is new, a step you leave out is
  deleted. Steps are numbered in the order you send them, which is also how you reorder them.
- An active scenario must keep at least one step and its default-language text.

### Add or replace one language

`PUT /scenarios/{id}/translations/{locale}` returns 204.

```json
{
    "title": "Een bestelling aanmaken",
    "description": "…",
    "preconditions": "…",
    "steps": [
        { "id": 1, "title": "…", "instruction": "…", "expected_result": "…", "link_label": null, "url": null }
    ]
}
```

Every step needs the `id` of a step of this scenario.

### Change the status

`PUT /scenarios/{id}/status` with `{ "status": "active" }`.

```json
{ "data": { "id": 1, "status": "active" }, "warnings": ["Step 2 has no nl translation."] }
```

Activation fails with 422 when the scenario has no steps, or when the scenario or a step has no default-language
translation. Missing translations in other languages are returned as `warnings`.

### Flag ready for retest

`POST /scenarios/{id}/retest` returns `{ "data": { "id": 1, "ready_for_retest_at": "…" } }`. Testers who completed the
scenario before this moment see it marked as ready for retest.

## Results

`GET /scenarios/{id}/sessions` lists the sessions of a scenario, newest first, 15 per page (`?page=2`). The sessions
are in `data`; `total`, `current_page` and `last_page` describe the pages.

`GET /sessions/{id}` returns `{ "data": session }`.

```json
{
    "id": 1,
    "scenario_id": 1,
    "tester": { "type": "App\\Models\\User", "id": 7 },
    "locale": "en",
    "status": "completed",
    "result": "accepted_with_feedback",
    "closing_comment": null,
    "started_at": "…",
    "completed_at": "…",
    "steps": [
        {
            "id": 1,
            "step_id": 1,
            "position": 1,
            "title": "…",
            "instruction": "…",
            "expected_result": "…",
            "link_label": null,
            "url": "/orders/create",
            "verdict": "feedback",
            "feedback": "The form stays empty after saving.",
            "feedback_status": "open",
            "resolution_note": null,
            "resolved_at": null,
            "context": {
                "page_url": "https://app.test/orders/create",
                "user_agent": "…",
                "viewport_width": 1280,
                "viewport_height": 720,
                "app_locale": "en",
                "judged_at": "…"
            }
        }
    ]
}
```

- `status` is `in_progress`, `paused`, `completed` or `abandoned`.
- `result` is `accepted` or `accepted_with_feedback` on a completed session, otherwise `null`.
- `verdict` is `accepted`, `feedback` or `null` when the step has no verdict yet.
- The steps are the text the tester saw. Later edits to the scenario do not change them.

## Feedback

`GET /feedback` lists feedback across all scenarios, newest first, 15 per page. It returns open feedback unless you
pass `?status=resolved`. Pass `?scenario={id}` for one scenario.

```json
{
    "id": 1,
    "scenario_id": 1,
    "session_id": 1,
    "tester": { "type": "App\\Models\\User", "id": 7 },
    "locale": "en",
    "step": { "position": 1, "title": "…", "instruction": "…", "expected_result": "…", "url": "/orders/create" },
    "feedback": "The form stays empty after saving.",
    "status": "open",
    "resolution_note": null,
    "resolved_at": null,
    "context": { "page_url": "…", "user_agent": "…", "viewport_width": 1280, "viewport_height": 720, "app_locale": "en", "judged_at": "…" }
}
```

`POST /feedback/{id}/resolve` with an optional `{ "note": "Fixed in the order form validation." }` returns 204.

## Errors

| Status | Meaning |
|---|---|
| 401 | The token is missing, unknown or expired. |
| 403 | The token may not write. |
| 404 | The record does not exist, or the API is switched off. |
| 409 | The scenario has sessions and cannot be deleted. |
| 422 | Validation failed. `errors` lists the messages per field. |
