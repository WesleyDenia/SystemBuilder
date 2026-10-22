# Reviewer gate — architecture-spine rubric

**Scope reviewed:** `architecture-systembuilder-control-plane-kernel.md`, draft dated 2026-10-21.  
**Mechanical check:** `lint_spine.py` passed with 0 findings.

## Verdict

**Pass with corrections before finalization.** The spine is appropriately concise for initiative altitude, establishes the principal cross-unit constraints, and covers the SaaS operational envelope at the topology level. The findings below are real divergence points: tenancy evolution, operational continuity, runtime selection, and webhook trust must be made invariant or explicitly deferred.

## Checklist assessment

| Good-spine check | Assessment |
| --- | --- |
| Fixes real divergence points for the level below | Largely passes: module direction, BFF boundary, Control/Execution ownership, tenant scope, state mutation, data classes, runner isolation, provider capability, infrastructure separation, customer delivery, BMAD route, and project context are bound. |
| Rules enforce their stated `Prevents` clauses | Largely passes. AD-1 to AD-3, AD-5, AD-7, AD-8, AD-10 to AD-12 are actionable. AD-4 needs its future dedicated-tenancy condition clarified. |
| Deferred prevents avoidable divergence | Partial. Several implementation choices have appropriate triggers; runner runtime and operational continuity have no sufficiently explicit deferred decision. |
| Appropriate initiative altitude | Passes. It fixes architecture boundaries and seeds without prescribing payload/data-model internals. |
| Input/source reconciliation | Partial. The 0006/PRD precedence is explicitly stated and UX constraints are represented. The product-scope mismatch is correctly surfaced in `review-prd-reconciliation.md`, but finalization depends on updating that PRD. |
| Operational/environmental envelope | Partial. Public/private/network/runner topology and scale boundaries are present; recovery, upgrade/migration, and webhook ingress trust are silent. |
| Named technology verified/current | Partial. Application/data stack entries are pinned; the OCI runtime remains an unpinned either/or choice. |

## Findings

### RG-1 — High — AD-4 conflicts with the deferred dedicated-tenancy path

AD-4 names “Shared-schema tenancy” as mandatory, while Deferred permits future “dedicated tenant pools, and database-per-tenant.” Two builders could reasonably treat the latter as an allowed exception to shared schema or as a conflicting replacement, with different query, migration, and authorization behavior.

**Action: autofix.** Rename/limit AD-4 to the initial shared-schema deployment and bind the enduring invariant instead: Tenant/Project context, fail-closed authorization, scoped credentials and equivalent isolation must hold for every tenancy topology. State that a dedicated topology changes only the persistence adapter, not the authorization or scope contract.

### RG-2 — High — Operational continuity is not decided or deferred

AD-6 names backups as storage content, but no rule establishes backup encryption, restore authority/testing, recovery objectives, schema migration/rollback ownership, or how an unavailable MySQL/Redis/S3 dependency changes governed transitions. The Deferred list covers monitoring/SLOs and retention but not recovery or upgrade safety. This is a whole operational dimension at initiative/SaaS altitude, not an implementation detail.

**Action: discuss then defer.** Add a narrowly scoped operational-continuity AD (if a baseline policy is known) or a Deferred entry with a pre-production trigger that covers backup/restore validation, migration rollback/forward policy, data-service failure behavior, and RPO/RTO ownership. Exact values/vendors can remain deferred.

### RG-3 — Medium — Rootless runner runtime is not a stable stack decision

The Stack permits a “supported Docker or Podman release pinned in Runner Manager build manifest.” Docker versus Podman affects image/build behavior, rootless networking, socket assumptions and hardening. This leaves independently-built Runner Manager, CI and infrastructure units able to choose incompatibly. It also evades the otherwise pinned-stack convention.

**Action: autofix or defer.** Select one initial runtime and pin it, or add a Deferred item requiring the Runner Manager contract/build-manifest owner to select one before any runner, CI image or provisioning implementation begins; retain a versioned OCI interface so later replacement is controlled.

### RG-4 — Medium — “Authorized webhooks” has no enforceable ingress contract

The Structural Seed accepts authorized webhooks into the Control Plane, but no AD says who verifies provider identity/signature, timestamp/replay, Tenant/Project correlation, idempotency, or quarantine/reconciliation of invalid deliveries. This can reintroduce cross-tenant mutation and duplicate state despite AD-3/AD-4.

**Action: autofix.** Extend AD-3 or AD-4 with a webhook rule: Laravel verifies an adapter-specific signature and replay/idempotency before a command; scope is derived from verified integration binding, not payload IDs; invalid/unmatched deliveries are rejected or quarantined without governed mutation. Payload format and retry timing may remain deferred.

### RG-5 — Medium — The PRD reconciliation is a finalization dependency

The spine properly states that 0006 wins over the Kernel-only PRD, but its full SaaS scope still exceeds the PRD’s stated product boundary. Without updating the PRD, downstream ticketing can legally exclude the very Runner, Git, delivery and operational units that this initiative spine binds.

**Action: block final status pending source update.** Apply the prepared PRD reconciliation prompt with the product-management/PRD agent, then rerun input reconciliation; this is not a reason to weaken the spine.
