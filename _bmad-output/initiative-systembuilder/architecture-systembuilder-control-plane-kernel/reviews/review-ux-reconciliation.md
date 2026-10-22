# UX Input Reconciliation — SystemBuilder SaaS Architecture Spine

**Input reviewed:** `../ux-systembuilder-control-plane-kernel/DESIGN.md` and `../ux-systembuilder-control-plane-kernel/EXPERIENCE.md`  
**Target reviewed:** `../architecture-systembuilder-control-plane-kernel.md`  
**Verdict:** **Changes required before finalization.** The spine carries the primary UX architectural constraints (BFF-only browser boundary, server-derived tenant/project scope, append-only governed state, isolated runners, no provider fallback, sanitized errors, WCAG/localization, and desktop-only critical actions). Four UX requirements still need explicit architectural binding because independent implementation could otherwise diverge.

## Landed constraints

- The Next.js Console/BFF has no business authority and Laravel reauthorizes identity, Tenant, Project, and action scope (AD-2, AD-3); this supports the authorized Project context and non-disclosure UX requirements.
- Shared-schema scope, fail-closed repositories/policies, scoped object URLs and execution credentials (AD-4) support the cross-Tenant/Project non-enumeration requirement.
- Versioned declarative workflows, append-only Ledger facts, immutable Baselines/artifacts/decisions, declared guards and Exit Contracts (AD-5) support Readiness, Gate, lineage, audit, and traceability flows.
- Isolated runners and scoped endpoints (AD-7), plus independent versioned Capability/credential/executor records and explicit incompatibility failure (AD-8), support the execution and homologation surfaces.
- The Console convention explicitly adopts WCAG 2.2 AA, the four locales, and read-only mobile treatment for critical actions (architecture lines 108–113).

## Findings

### UXR-1 — Execution-result intake lacks a correlation-validation and quarantine invariant

**Severity: High**

`EXPERIENCE.md` requires Artifact, Evidence, Findings, and events without valid correlation to be rejected or quarantined for reconciliation (lines 183–192). The spine requires idempotent consumers by correlation ID (line 110), scoped runner endpoints (AD-7), and that Laravel alone accepts results (AD-3), but it does not bind a result to an authorized Execution, its project scope, contract/version, and expected output manifest before acceptance, nor name a quarantine outcome.

**Required spine change:** Add an AD or extend AD-3/AD-7 so the Runner Manager/result-ingress contract validates execution ID/token, Tenant/Project, correlation ID, contract/version, and declared output identity; invalid, duplicate-conflicting, or uncorrelated submissions append an auditable rejection/quarantine fact and cannot update projections, Gates, Baselines, or accepted artifacts.

### UXR-2 — Downstream impact completion is not an explicit progression guard

**Severity: High**

The UX requires exactly one disposition per affected relation, requires `REQUIRES_REVIEW` rather than `unchanged` when a relation/declaration is absent or materiality is uncertain, and keeps dependent authorization/closure ineligible while a manually deferred analysis lacks a disposition (lines 113–115, 214–224). AD-5 names impact and declared guards generally, but does not bind this completeness rule, failure behavior, or the fact that re-opening must never automatically mutate/delete downstream artifacts.

**Required spine change:** Extend the Workflow/Governance invariant with a versioned impact-analysis contract: after a material upstream revision, each known relation receives an append-only disposition; incomplete/failed/unknown analysis is pending and blocks dependent actions; only a declared, evidenced `unchanged` clears the guard; `STALE` and `REQUIRES_REVIEW` produce a navigable dependent work item; neither path mutates a downstream artifact automatically.

### UXR-3 — Override and Formal Exception need separate domain invariants

**Severity: Medium**

The UX makes Override a narrow, policy-declared exception for an eligible `CONCERNS`/`FAIL` check and transition, while `BLOCKED` categorically rejects generic Override. A `BLOCKED` progression can occur only under a separately predeclared Formal Exception with bounded transition/actions, authority, expiry/closure, compensating control, and preserved blocker/risk (lines 154–163, 194–212). AD-5 covers generic policy/gate/transition declarations but does not prevent an independently-built module from treating the two mechanisms as a single bypass record.

**Required spine change:** Bind Override and Formal Exception as separate governed decision types in Workflow/Governance. Preserve the original result, constrain both to declared policy/version/scope, and specify that a Formal Exception never changes `BLOCKED` to `PASS` and expires/ends back at the governed state.

### UXR-4 — Authorized Evidence presentation is not a BFF/API contract

**Severity: Medium**

AD-4 scopes access and AD-8 says secrets are redacted from UI, but the UX requires an authorized presentation model before rendering Evidence: only safe labels/references, no raw paths/URLs, and the same redaction over rendered text, `href`, accessible names, tooltips, toasts, client logs, copied URLs, and query strings; inaccessible Evidence must not confirm existence (lines 169–177). Without an explicit server-owned presentation contract, the Console and BFF could implement different redaction and disclosure behavior.

**Required spine change:** Add a Console/BFF contract rule that Laravel returns classified, authorization-filtered presentation DTOs for Evidence and audit data. The DTO must be safe for every client-visible channel, distinguish permitted metadata from preview content, and yield a generic non-enumerating response for inaccessible resources; raw storage locations and secret-bearing source payloads never cross the Control Plane-to-BFF boundary.

## Correctly outside this architecture spine

The visual tokens and component styling in `DESIGN.md`; keyboard/focus and responsive conversion details; exact Ledger filters, table/list presentation, pagination interaction; copy/voice; and particular shadcn component behavior are rightly retained in UX. They are implementation-level Console constraints, not cross-unit architectural decisions. The same applies to the exact three Stage Profiles used by a Contract Test: the spine already binds capability homologation/no fallback, while fixtures and first supported combinations remain appropriately deferred.

## Contradictions

No direct contradiction found. The target architecture is broader than the UX package, as intended, and its full-SaaS boundaries do not weaken the UX constraints above.
