# Tracer v1 STRIDE threat model

## Scope, flow, and assets

The test-only flow is Browser → `GET /api/tracer` on Next.js → server-created short-lived delegation → Laravel internal tracer adapter → Application membership authorization → fixture repository. The verifier alone reads invocation evidence through a second test-only Laravel adapter protected by a per-run verification key. Assets are the delegation HMAC key, verification key, delegation claims, Tenant/Project/action scope, sanitized fixture, invocation evidence, and process output/assets.

| Category | Threat and control | Evidence / residual risk | Owner |
| --- | --- | --- | --- |
| Spoofing | Forged BFF callers or claims are rejected by HMAC, fixed issuer/audience/action, strict claim allowlist, exact scope authorization, and loopback test harness. | Laravel feature tests and `verify:tracer` cover malformed, wrong audience/action, and out-of-scope values. The harness is not production identity. | Control Plane |
| Tampering | Unknown claims, malformed encodings, altered signatures, incompatible schemas, and browser scope parameters fail closed; BFF has a GET-only bounded-input route. | Contract schemas, feature tests, BFF noisy-input assertions, and structural check. Residual: no production API gateway/rate limit is selected. | Console + Control Plane |
| Repudiation | Per-request `jti`, bounded expiry, replay guard, and verifier correlation make a scoped-read invocation measurable. | Replay tests and invocation evidence assertions prove one success/zero denials. Evidence is in-memory and test-only, not an audit ledger. | Control Plane |
| Information disclosure | Browser responses are allowlisted v1 objects; denial bodies are fixed; server keys/URLs/delegations are not public variables and verifier scans responses, logs, and static assets. | Verifier leak assertions. Residual: local development process output is not a production logging policy. | Console |
| Denial of service | BFF accepts GET only, rejects bodies and oversized URLs, and has a two-second upstream timeout; verifier has bounded readiness and cleanup. | Verifier checks method and size rejection. Residual: no production rate limiting or queue policy exists by design. | Console |
| Elevation of privilege | Laravel validates delegation, exact fixture membership, tenant/project/action, and replay before query; BFF has no membership decisions. Test routes require `APP_ENV=testing`. | Application feature tests, structural check, and direct-denial verifier cases. Residual: production IdP/RBAC remains owned by the future identity story. | Control Plane |

This model is versioned with the tracer contract and is intentionally limited to the local fixture harness. It does not approve deployment or production identity use.
