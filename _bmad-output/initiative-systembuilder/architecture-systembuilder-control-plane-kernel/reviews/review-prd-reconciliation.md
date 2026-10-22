# PRD reconciliation — SystemBuilder SaaS Reference Architecture

**Input reviewed:** `prd-systembuilder-control-plane-kernel.md` (2026-10-14) against `architecture-systembuilder-control-plane-kernel.md` (draft, 2026-10-21).

## Verdict

**Not reconciled — revision of the PRD is required before it can remain the product authority for this spine.** The architecture deliberately covers the full SaaS reference system, while the PRD is explicitly a Kernel MVP that excludes several architectural capabilities now bound by the spine. The governance core substantially lands, but the required BMAD flow and the secret/result-ingestion guarantees need explicit architectural treatment.

## Findings

### RPR-1 — Critical — Product scope conflicts with the architecture scope

The PRD defines a “Control Plane Kernel MVP” and explicitly excludes physical architecture/stack, storage, queue, runner protocol, production infrastructure, complete repository/CI-CD/deploy integrations, and post-deploy operation (§4.2; OQ-17). The spine binds a whole SaaS scope and fixes Laravel/Next.js, MySQL, Redis, S3-compatible storage, Hetzner topology, Runner Manager, Git and Deployment Provider adapters (scope/frontmatter; AD-6 through AD-10; Stack; Structural Seed).

**Required reconciliation:** replace the Kernel-only product boundary with a full-SaaS scope and phase the former exclusions as capability increments/non-goals for the first release. The PRD must distinguish (a) SaaS delivery, (b) governed customer-project delivery, and (c) the Kernel governance core, matching AD-10.

### RPR-2 — High — The mandatory BMAD flow is not a named architectural invariant

PD-11, FR-23, NFR-14 and SM-8 require the governed order `PRD → UX → Architecture → Project Context → Coding/Story`, with provenance, approval, traceability, revalidation after upstream changes, and no agent-induced skip. AD-5 provides generic declarative/versioned workflows and gates, but neither it nor the Capability → Architecture Map identifies the mandatory profile, Project Context, or the rule that *recommendations and authorizations* must be checked against it.

**Required reconciliation:** add an AD or an explicit binding rule under AD-5 that makes the versioned flow profile a Laravel governance concern; include the five required artifacts/stages, validation/approval identity, downstream re-evaluation, and transition-specific exceptions. Do not delegate this authority to BFF, BMAD, or Runner Manager.

### RPR-3 — High — Secret-redaction boundary is incomplete against the PRD

NFR-6 prohibits secrets in browser, Ledger, runner stdout/stderr persisted output, prompt archive, Artifact and Evidence. AD-8 requires redaction from prompts, logs, Ledger and UI, but does not name persisted runner streams, artifacts or evidence; AD-6 makes artifacts/exports durable, increasing the consequence of omission.

**Required reconciliation:** extend AD-8 (or add a dedicated invariant) so collection, storage, export and display paths reject/redact secrets before data becomes an Artifact, Evidence, runner-log archive, prompt archive, or Ledger fact. The PRD should then specify the product-facing policy for detection, rejection/quarantine, retention and authorized access.

### RPR-4 — Medium — Result acceptance/reconciliation contract remains too implicit

FR-9 requires result-to-Execution correlation, logical quarantine for unmatched results, recorded resolution of material BMAD/Control Plane divergence, and loss-safe accepted events. AD-3 says only approved commands accept a result, and AD-7 says runner loss cannot change accepted state, but no invariant assigns validation/correlation/quarantine/reconciliation behavior at the Control Plane boundary.

**Required reconciliation:** bind result ingestion to a Laravel application command that verifies execution token, Tenant/Project scope, correlation, contract/version and idempotency before accepting facts; unmatched or divergent material must enter a governed reconciliation/quarantine state. Keep exact payload/protocol/retry encoding deferred, as the spine currently does.

### RPR-5 — Medium — Several Kernel OQs are still deferred correctly, but need explicit PRD status after scope expansion

The spine defers exact external IDs/cardinalities, runner protocol and queue schema, retry/backoff encoding, BMAD target/fixtures, retention/export/deletion, and production CI/CD/IaC/SLO details. These align with OQ-1, OQ-3, OQ-5 to OQ-10 and OQ-13, but the PRD phrases some as out of scope for the Kernel rather than decisions deferred behind architecture interfaces.

**Required reconciliation:** retain them as interface-bounded open questions in the revised PRD, each with its implementation trigger. In particular, do not promote a concrete runner protocol, provider/executor, BMAD version, retention duration, SLO, or infrastructure sizing merely because the full-SaaS spine now owns the enclosing boundary.
