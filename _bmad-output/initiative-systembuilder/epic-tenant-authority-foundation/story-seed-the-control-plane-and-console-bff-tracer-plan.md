---
title: 'Seed the Control Plane and Console/BFF tracer'
type: 'feature'
ticket: '1'
created: '2026-10-29'
status: 'built'
route: 'full'
route_source: 'auto'
review: 'thorough'
review_source: 'pinned'
lenses_ran: ['blind-hunter', 'edge-case-hunter', 'verification-gap', 'intent-alignment']
review_loop_iteration: 0
baseline_revision: 'c0c76591d0dff21497b1c413f6f5913fcee3bda3'
context:
  - 'AGENTS.md'
  - '_bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md'
  - '_bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** The repository has governance artifacts but no application seed, shared request contract, or canonical check proving that a scoped request reaches Laravel only through the Console/BFF boundary. Foundation work cannot safely begin without a runnable boundary tracer.

**Approach:** Add a minimal Laravel Control Plane and Next.js Console/BFF seed, a versioned test-only scoped-read contract, and one canonical Docker Compose verification command. The browser-facing route will call Laravel server-to-server with a fixture-only delegation; direct calls to the Laravel tracer route will be rejected. Node.js and PHP execute only inside the Compose services, never as prerequisites of the developer host.

## Boundaries & Constraints

**Always:** Keep business and scope seams in Laravel `Domain`/`Application`, with HTTP and fixture storage as adapters; keep the browser limited to a Next.js BFF route; derive fixture scope on the server, fail closed for missing/invalid delegation, keep the delegation value server-only and redacted, and make the check deterministic. Establish the structural seed and commands required by the story without claiming production identity, persistence, authorization, or deployment readiness.

**Never:** Choose or integrate an IdP, MFA, RBAC/ABAC claims model, database schema, Redis, Ledger/outbox, runner, provider, object storage, or production secret mechanism. Do not expose a Control Plane base URL or delegation credential to browser code, accept Tenant/Project scope from request query/header/body, add a direct browser Laravel route, or treat this tracer as a governed mutation.

**Decision (2026-11-05, human-renegotiated):** Pin Node.js 24.22.1 and PHP 8.5.12 in Docker images and run the tracer through Docker Compose. The host needs Docker Compose only; neither Node.js nor PHP on the host participates in setup or verification. Laravel is reachable only over the private Compose network, while the verifier is a short-lived Compose service that owns per-run fixture material.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| BFF tracer | Browser requests the public Next.js tracer endpoint | BFF injects the server-held test delegation and returns the sanitized scoped-read fixture supplied by Laravel | No Control Plane address or delegation material appears in the response |
| Direct Control Plane attempt | Request lacks the valid fixture delegation | Laravel rejects the tracer adapter without returning scoped fixture data | Return a sanitized denial; log no secret |
| Invalid fixture delegation | BFF-to-Laravel request has expired, malformed, or wrong-audience fixture delegation | Laravel does not call the application query or expose scope | Return a sanitized denial and let the verifier fail clearly |

</frozen-after-approval>

## Security Addendum (2026-11-04)

The approved fixture harness is implemented with application-owned membership authorization, single-use bounded delegations, strict v1 schemas, and test-only invocation evidence. The versioned threat model is [STRIDE.md](../../../contracts/tracer/v1/STRIDE.md). It records residual ownership for production identity, audit, rate limiting, and logging without expanding this story into a production identity design.

## Code Map

- `compose.yaml`, `docker/`, and `scripts/` -- add pinned Node, PHP, and short-lived verifier services. The Control Plane must have no host port and the browser-facing BFF remains the only public application boundary.
- `package.json` -- preserve workspace metadata but do not make the canonical verification command require host Node.js.
- `.gitignore` -- already excludes `.env`, `vendor`, `node_modules`, and Laravel runtime paths; preserve those protections for the new seed.
- `console/` -- absent; create the Next.js presentation/BFF seed. Browser-visible code must have no Laravel URL or delegation token.
- `control-plane/` -- absent; create the Laravel seed and the `Domain/`, `Application/`, `Infrastructure/`, and `Http/` conventions declared by Architecture AD-1.
- `contracts/` -- absent; add the versioned tracer request/response contract shared as a stable boundary description, not a generated client.
- `scripts/` and `README.md` -- establish the sole documented Docker-only verification command; it owns Compose setup and teardown so each run starts with fresh service state and fixture material.
- `_bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/story-seed-the-control-plane-and-console-bff-tracer.md` -- source story; do not alter its ticket body or tracker fields.

## Tasks & Acceptance

**Execution:**
- [ ] `compose.yaml`, Dockerfiles, workspace configuration, and `README.md` -- define a Docker-only developer entry point, private service network, pinned component images, and the canonical Compose verification command -- makes the seed discoverable and repeatable without host Node.js or PHP.
- [ ] `contracts/tracer/v1/` -- add a small versioned scoped-read request/response contract and explicit fixture-only delegation semantics -- prevents the BFF and Control Plane from silently inventing incompatible payloads.
- [ ] `control-plane/` Laravel seed, `app/Domain/Tenancy/`, `app/Application/Tenancy/`, `app/Infrastructure/Tenancy/`, and `app/Http/` -- implement a fixture-backed scoped-read query behind a server-to-server-only tracer adapter; validate delegation before the application query and reject missing, malformed, expired, or wrong-audience input -- establishes the inward dependency and fail-closed boundary without selecting production identity or persistence.
- [ ] `console/` Next.js seed and BFF tracer route -- expose one browser-safe endpoint that obtains its fixture delegation only in server execution, calls Laravel, and returns only the sanitized contract response -- demonstrates AD-2 without granting BFF domain authority.
- [ ] `scripts/verify-tracer.*` and component tests -- run the BFF, Control Plane, and short-lived verifier as Compose services with test-only configuration, call the BFF endpoint, assert the returned scoped fixture, and prove the equivalent direct Laravel request is denied -- creates the story’s canonical cross-boundary evidence without host runtimes.
- [ ] `.env.example` files and ignore-safe test fixtures -- document variable names and non-secret placeholders only -- keeps credentials out of source, browser bundles, and test output.

**Acceptance Criteria:**
- Given a host with Docker Compose and no installed Node.js or PHP requirement, when the canonical verifier runs, then it proves a test fixture crosses the browser-facing Next.js BFF route to a Laravel application query and returns only the authorized scoped response.
- Given a caller attempts the Laravel tracer endpoint without the server-held fixture delegation, when Laravel receives the request, then it denies access without returning Tenant or Project fixture data.
- Given the Console build and repository search, when inspected, then no browser-visible code, public environment variable, or response exposes the Laravel base URL or fixture delegation.
- Given the structural seed, when a maintainer inspects its modules, then Laravel has explicit `Domain`, `Application`, `Infrastructure`, and `Http` seams and the BFF contains presentation/session/API-client work only.

## Implementation Notes

## Plan Change Log

- 2026-11-05 — Docker verification command is `sh scripts/verify-tracer.sh`; it is a Docker Compose wrapper that tears down services and volumes before and after the verifier so the documented command is repeatable without host Node.js or PHP.

## Review Triage Log

- medium / patch — `InMemoryFixtureDelegationReplayGuard` and `InMemoryScopedReadInvocationEvidence` retain `/tmp` state when Compose reuses a stopped test container; `scripts/verify-tracer.sh` now tears down services and volumes before each run, and two sequential canonical runs passed.
- medium / patch — running Control Plane and Console containers could retain the previous environment and never acknowledge fresh verifier configuration; the same canonical lifecycle recreates both services before each run, confirmed by two sequential canonical runs passing.
- medium / patch — `TracerDelegationRequest` accepted a missing or malformed correlation as `uncorrelated`; it now returns the existing sanitized denial before the use case, covered by the Compose PHPUnit suite (13 tests, 51 assertions).
- medium / patch — `control-plane/composer.json` had no lockfile, so a fresh Docker image resolved a mutable PHP dependency graph; `composer.lock` is committed and copied before `composer install`, verified by the Control Plane image build.
- SEC-01 — addressed by `contracts/tracer/v1/STRIDE.md`, linked Security Addendum, and the six-category control/evidence table.
- SEC-02 and adversarial findings 1–2 — `MembershipAuthorizer`, `InMemoryFixtureMembershipAuthorizer`, Laravel out-of-scope tests, and verifier tenant/project/action denial cases reauthorize exact scope before read.
- SEC-03 and adversarial finding 4 — `APP_ENV=testing` route registration and `TRACER_HARNESS_ENABLED` make test adapters fail closed outside the harness.
- SEC-04 and adversarial findings 5 and 14 — strict v1 request/response/error schemas, exact delegation claims, fixed sanitized errors, and incompatibility denial tests close contract drift.
- SEC-05 — HMAC, issuer/audience/action equality, 120-second expiry, `jti`, and `FixtureDelegationReplayGuard` are exercised by replay and malformed-claim tests.
- SEC-06 and adversarial finding 7 — `ScopedReadInvocationEvidence` proves one authorized query and zero denied queries in PHPUnit and the verifier.
- SEC-07 and adversarial finding 3 — the BFF ignores browser scope selectors, rejects body/oversized requests, bounds upstream timeout, and verifier covers noisy query/header/cookie plus repeated calls.
- SEC-08 and adversarial finding 11 — fixed error bodies plus verifier response/log/static-asset leak scans enforce response/output sanitization.
- SEC-09 and adversarial findings 8–10 and 12–13 — runtime preflight, isolated lifecycle with cleanup, server-only per-run keys, loopback binding, and structural checks are implemented in `scripts/verify-tracer.mjs` and `scripts/check-tracer-structure.mjs`.
- medium / patch — the BFF readiness probe performed an authorized read before the three asserted reads, producing a fourth evidence entry; it now probes the static Console root before making the three explicit BFF reads, verified by two clean Compose cycles.
- low / patch — a chunked GET body could bypass the `Content-Length` check; the BFF now rejects any non-null request body before the server-side delegation is created.
- low / rejected — Next.js's default unsupported-method 405 body is outside the tracer error schema, but callers receive the correct denial status and changing all method handlers adds unrelated surface.
- false — runtime code already enforces the exact response shape and strict delegation claims; parsing the declarative JSON schemas would not prove an additional reachable behavior.
- medium / patch — wrong issuer and modified HMAC signature were not represented among denial evidence; canonical and PHPUnit denial suites now assert zero reads for both.
- low / defer — the disabled-harness BFF route lacks an isolated Console test, but the runtime gate is explicit and its test runner is outside this narrowly scoped integration correction.
- false — `APP_ENV=production php artisan route:list --path=api/internal/tracer` returned no routes, proving the Laravel internal adapters remain harness-only at runtime.
- low / defer — STRIDE's stated log scan is broader than the present verifier scan; this documentation/evidence alignment is not a cause of the invocation-count defect.
- low / defer — the unrelated BMad review-skill workflow observations are outside the tracer correction intent.
- false — the replay guard's local storage is an explicitly bounded single-container harness, not the production replay-store design excluded by the frozen intent.
- low / defer — structural substring checks are limited but no concrete cross-boundary bypass was demonstrated by the reviewed change.
- low / defer — an oversized successful Control Plane body and a malformed successful BFF upstream response lack fault injection; neither is involved in the reproduced tracer evidence defect.
- medium / patch — replay and invocation stores did not check truncated/partial writes; both now fail closed, so evidence or replay state cannot silently report success after an incomplete persistence operation.
- medium / patch — invocation-evidence authorization and validation errors had no HTTP coverage and could use Laravel's default response shape; the FormRequest now emits sanctioned sanitized errors and a feature test exercises the actual controller route for valid, absent, wrong-key, and invalid-correlation requests.
- false — the direct fixture call used solely by the private verifier to test replay is not a browser-to-Control-Plane route; browser traffic still reaches Laravel only through the Console/BFF and cannot hold the delegation.

## Design Notes

The fixture delegation is deliberately a bounded test harness, not an identity design: it models the BFF-to-Laravel audience boundary while story 1.2 retains ownership of IdP, claims, membership lookup, revocation, and authorization policy. The Laravel tracer must validate that harness before invoking its application query so later identity work replaces an adapter seam rather than moving policy into Next.js.

## Verification

**Commands:**
- `uv run _bmad/scripts/setup_check.py` -- expected: BMAD installation/configuration check succeeds.
- `sh scripts/verify-tracer.sh` -- expected: Docker-only BFF-mediated scoped read succeeds; direct, malformed, expired, wrong-audience, and malformed-correlation Control Plane requests are denied; output contains no delegation value, persistent fixture material, or host Node.js/PHP invocation.
