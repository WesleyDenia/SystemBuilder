# Tracer delegation v1

This is a test harness contract, not a production identity protocol. The Console BFF holds a delegation only in its server process and sends it to Laravel in `X-Fixture-Delegation`.

The compact value is `base64url(json).base64url(hmac-sha256(payload, TRACER_DELEGATION_KEY))`. The JSON payload has exactly these fields, in this order: `iss`, `aud`, `exp`, `jti`, `tenant_id`, `project_id`, and `action`. Laravel requires `iss = console-bff-fixture`, `aud = control-plane-tracer`, `action = tracer.scoped-read`, a `jti` matching `[A-Za-z0-9-]{1,64}`, and a future UTC epoch `exp` no more than 120 seconds away. It verifies the HMAC before parsing claims, rejects an unknown, missing, malformed, overlong, out-of-scope, or replayed claim set, and atomically claims a `jti` before the application query. Any denial has the fixed, body-only error `{ "error": "tracer_access_denied" }`.

`TRACER_DELEGATION_KEY` is a per-run harness value held only in the BFF and Laravel server processes; the BFF signs a fresh delegation for each request. Compact delegations are never example values, browser configuration, output, or persistent fixtures. The request and response schemas reject unknown fields; unsupported v1 data fails closed.
