---
tracker_id: "3"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/3"
tracker_status: backlog
id: 1
type: story
title: "Seed the Control Plane and Console/BFF tracer"
parent: epic-tenant-authority-foundation
covers: [CAP-15, CAP-16]
after: []
hitl: false
risk: high
refined: true
---

# Seed the Control Plane and Console/BFF tracer

## Description

Seeds the Laravel Control Plane, the Next.js Console/BFF, a versioned internal tracer contract and canonical verification commands. A test-only, server-held delegation crosses the BFF to a Laravel scoped read. It is a bounded identity harness, not a production identity or authorization design.

## Acceptance Criteria

1. Given a fresh clone at the repository root with Node.js 24.22.1 and PHP 8.5.12, when an independent maintainer runs `npm ci`, `npm run setup:tracer` and `npm run verify:tracer` in that order, then the verifier exits successfully only after proving that the browser-facing BFF route returns the authorized scoped fixture from a Laravel application query; an unmet prerequisite or failed check exits unsuccessfully with a sanitized failure.
2. Given a test-only membership seam containing an authorized fixture membership, when Laravel receives a valid delegation, then Laravel reauthorizes its Tenant, Project and action against that seam before the scoped read. The seam is unavailable to production routes and does not select an IdP, MFA or production claims model.
3. Given a missing, malformed, expired, wrong-audience or well-formed but out-of-scope test delegation, when `npm run verify:tracer` exercises the tracer, then each request is denied before the scoped read, invokes no scoped query, returns no fixture bytes and emits only sanitized denial evidence.
4. Given browser-supplied Tenant, Project or action values, when `npm run verify:tracer` invokes the BFF tracer, then they cannot select or alter the scoped fixture; the test-only server-derived context remains authoritative and any denial is sanitized.
5. Given the production Console build, public configuration and tracer responses, when `npm run verify:tracer` checks their emitted assets and requests, then none contains the Laravel base URL, the internal delegation or another internal service route, and the browser has no direct Laravel route.
6. Given the shared BFF-to-Laravel tracer contract, when either side sends an incompatible request or response, then the canonical verifier fails rather than accepting an implicit fallback.
7. Given the seeded Control Plane and Console/BFF, when `npm run verify:tracer` runs its structural check, then it confirms Laravel Domain, Application, Infrastructure and HTTP seams and confirms that the BFF has only presentation, session and server-to-server API-client responsibilities.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, Stack and Structural Seed
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-1–AD-4 and Consistency Conventions
- _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, FR-1, FR-2, NFR-1, NFR-2 and NFR-4

## Notes

- Decision (2026-11-04): the story delivers explicit component manifests, lockfiles, non-secret environment examples and canonical commands; their versions and prerequisites must be documented so verification is repeatable.
- Decision (2026-11-04): `npm run verify:tracer` is the sole canonical verification command for this story; it requires Node.js 24.22.1 and PHP 8.5.12, reports only sanitized pass or denial evidence and fails closed on an unmet prerequisite or check.
- Decision (2026-11-04): from a fresh repository root, `npm ci` installs root dependencies and `npm run setup:tracer` installs or prepares only the declared tracer component dependencies before `npm run verify:tracer`; no global dependency, secret or undeclared service is a prerequisite.
- Decision (2026-11-04): the fixture delegation exists only in the test harness and is unavailable to production routes or builds. It does not select an IdP, MFA, claims model, membership lookup or RBAC/ABAC policy; those remain owned by Story 2 and OQ-1.
- Decision (2026-11-04): the tracer uses a deterministic, in-memory test-only membership adapter so Laravel can reauthorize the fixture Tenant, Project and action. It is a replaceable application seam, not a production membership implementation; Story 3 owns the production authorization boundary and scoped persistence.
- Decision (2026-11-04): this tracer makes no governed mutation and does not replace the scoped-persistence and application-authorization work of Story 3.
