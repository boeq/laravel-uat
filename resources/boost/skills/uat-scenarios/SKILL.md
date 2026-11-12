---
name: uat-scenarios
description: "Write, translate, publish and maintain user acceptance test scenarios for the boeq/laravel-uat testing bubble, and work through the feedback testers leave. Use when asked to create test instructions or a UAT scenario for a feature, to update or archive a scenario, to summarise test results, or to resolve tester feedback."
license: MIT
metadata:
  author: boeq
---
# UAT scenarios

Testers run scenarios in a bubble inside the application. A scenario is a list of steps; every step ends as accepted
or as feedback. You manage scenarios and read results through an API.

## Connection

- Base URL: the `UAT_API_URL` environment variable, for example `https://app.test/api/uat/v1`.
- Token: the `UAT_API_TOKEN` environment variable. Send it as `Authorization: Bearer $UAT_API_TOKEN`.
- Never print the token, never write it to a file and never put it in a scenario.
- A token is read-only unless it was created with `php artisan uat:create-token <name> --write`. A 403 on a write
  means the token is read-only; ask the user for a token that may write.

```bash
curl -s "$UAT_API_URL/configuration" -H "Authorization: Bearer $UAT_API_TOKEN"
```

Every endpoint, with request and response bodies, is in [references/api.md](references/api.md).

## Creating a scenario

Follow these steps in order.

1. **Ask for the languages.** Fetch `GET /configuration`, then ask the user which of the supported languages to write
   in, with the default language pre-selected. Ask this every time and never assume.
2. **Understand the feature.** Use the user's description, the code changes, the routes and the UI to work out what a
   tester must do and what they should see.
3. **Check for overlap.** Fetch `GET /scenarios`. Update or archive an existing scenario instead of creating a
   duplicate.
4. **Draft.** Write the title, description, preconditions and steps in the default language, with links to the real
   routes.
5. **Confirm.** Show the draft to the user and adjust it to their feedback.
6. **Translate.** Write a natural translation for every chosen language. Keep the same steps in the same order.
7. **Publish.** Create the scenario with `POST /scenarios`. It starts as a draft. Activate it with
   `PUT /scenarios/{id}/status` once the user agrees, and report any warnings the response lists.

## Managing scenarios

- **Summarise results.** `GET /scenarios` returns result counts per scenario. `GET /scenarios/{id}/sessions` and
  `GET /sessions/{id}` give every verdict with its captured context.
- **Work through feedback.** `GET /feedback` lists open feedback. Turn each item into a concrete fix. After the fix,
  call `POST /feedback/{id}/resolve` with a note that says what changed, then `POST /scenarios/{id}/retest` so testers
  are invited to run the scenario again.
- **Keep scenarios current.** Update a scenario with `PUT /scenarios/{id}` when the feature changes. Send every step
  that should remain, with its `id`; steps you leave out are deleted. Archive a scenario when testing is done.
- **Delete with care.** A scenario with sessions cannot be deleted, only archived.

## Writing guidelines

- Write for non-technical testers. Use plain words, no class names and no jargon.
- One action per step, each with a clear expected result.
- Aim for 3 to 12 steps. Split a bigger feature into several scenarios.
- State the test data a tester needs: which account, which product, which values to enter.
- State the role a tester needs in the preconditions, so links do not lead to a page they cannot open.
- Add an edge case as its own step when it matters, such as an empty form or a wrong input.
- Links are a path inside the application, such as `/orders/create`, or a full `https://` URL.
- Never put passwords or other secrets in a scenario.

## When a request fails

- **422** lists what to correct, by field, for example `Step 3 is missing nl.instruction.`. Fix the request and send
  it again.
- **401** means the token is missing, unknown or expired. **403** means it is read-only. Tell the user; do not retry.
- **409** on delete means the scenario has sessions. Archive it instead.
