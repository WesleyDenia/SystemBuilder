# Reference Reconciliation Review — SystemBuilder SaaS

**Verdict:** **Changes required before finalizing.** The spine faithfully lands the architectural core of the binding reference (`0006`)—Control/Execution separation, Next.js BFF, Laravel authority, shared-schema tenancy, ephemeral rootless runners, Git, BYO-AI adapters, Hetzner network separation, and adapter-mediated delivery. It also absorbs much of `0005` through declarative workflows, gates, traceability, and append-oriented governance. The findings below are the remaining places where independently built components could still make incompatible choices.

## Inputs and treatment

- **Binding architectural source:** `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf`.
- **Product/workflow source:** `docs/0005_01-Fluxo_Desenvolvimento_Painel_BMAD.pdf`.
- **Reviewed spine:** `architecture-systembuilder-control-plane-kernel.md`, draft dated 2026-10-21.

## Findings

### RRC-01 — High — BFF-only rule conflicts with controlled direct object-storage transfers

`0006` §4.1 explicitly permits large uploads/downloads through short-lived presigned URLs as a controlled exception to BFF traffic. AD-2 says the browser “calls **only** Next.js BFF endpoints”; AD-9 then permits presigned object URLs. Read literally, the two spine rules conflict and an implementation can either break large transfers or bypass the intended BFF boundary.

**Required reconciliation:** amend AD-2 to state the sole exception precisely: browser-to-object-storage transfer is permitted only through a Laravel-authorized, BFF-issued (or BFF-mediated) short-lived, operation/object/scope-bound presigned URL; it must not expose internal APIs or storage credentials. Define upload completion/validation as a Control Plane command, not as evidence of an accepted artifact.

### RRC-02 — High — The mandatory workflow/gate baseline from 0005 is not binding

AD-5 requires that a workflow *can* declare reviewers, security policies, gates, and transitions, but it does not bind the minimum lifecycle and non-bypassable gates required by `0005` §§4, 7, 9–10: Idea → Discovery → Product Brief/Brainstorm → PRD → UX → Architecture → Epics → Stories; adversarial/security/readiness before development; test/code-review/security/verification before DONE; integrated technical/product/UX validation and retrospective before release. A separately-built Workflow module can therefore define a nominally compliant path that omits a required security review or marks a Story done merely on runner completion.

**Required reconciliation:** add an invariant for the canonical product workflow and its mandatory gate classes. Allow profiles to add/reorder non-mandatory stages, but require explicit governed policy/approval for any bypass, with the traceability edges to requirement, UX, ADR, story, execution, commit, test, security finding, and release preserved.

### RRC-03 — High — Cost/usage ownership and execution-abuse limits have no architectural owner

`0006` §§4.2, 6 and 7 assign cost/usage ledgering to the Control Plane and require per-tenant/project concurrency limits. AD-8 records provider/model policy and AD-7 imposes per-run resource limits, but neither makes Laravel the owner of provider/runner usage ingestion, budget/policy decisions, nor tenant/project concurrency reservation. The current “billing provider and plans” Deferred item does not cover these internal governance facts.

**Required reconciliation:** bind a Control Plane usage/limit boundary: Runner Manager and provider adapters report attributable usage to Laravel; Laravel authorizes/reserves/releases tenant/project concurrency and evaluates configured budget/usage policy. External billing and plan selection may remain Deferred, but must consume rather than replace this internal ledger.

### RRC-04 — Medium — Project Constitution and autonomy mode are not modeled as governed, versioned context

`0005` §§2 and 6 requires a project-wide Guided/Collaborative/Expert mode and a controlled Project Constitution (architecture/ADRs, security baseline, guardrails, allowed dependencies and prohibitions). AD-7 says runners get “approved context,” but names neither the owner nor a versioned, minimal-context contract; AD-5 does not cover the cross-workflow autonomy mode. Console, Workflow, and Runner implementations may then each infer different modes or context snapshots.

**Required reconciliation:** add a governed Project Constitution/context-profile model owned by Laravel, versioned and traceable to each execution; define a project-level autonomy mode which changes presentation/decision requirements without weakening Laravel gates or authorization. The detailed schema and prompt packing algorithm can be Deferred.

### RRC-05 — Medium — Queue/worker and real-time progress choices are implied but not decided or deferred

`0006` §§4.1, 8 and 10 uses Redis + Laravel Queue/Horizon for private job coordination, and permits BFF SSE/WebSocket progress while prohibiting browser access to queues/runners. The spine identifies Redis and independently scalable workers but does not name the queue/worker authority, progress-event path, or make either an explicit Deferred decision. This risks incompatible BFF polling/streaming and Runner Manager dispatch implementations.

**Required reconciliation:** either bind Laravel Queue/Horizon as the Control Plane dispatch/worker adapter and a server-mediated progress contract, or add a Deferred item requiring those two contracts before the first asynchronous execution/UI-progress implementation. Keep Redis inaccessible to runner containers and keep progress non-authoritative; Laravel remains the state-transition authority.

## Correctly deferred / not findings

The spine appropriately leaves provider/executor combinations, first BMAD inventory, runner protocol payload details, exact IaC/CI/CD, regions/sizing/firewall details, retention values, egress-proxy timing, stronger sandboxing, and enterprise isolation topology for later decisions. Those are compatible with `0006` as long as the listed revisit conditions are honoured.

