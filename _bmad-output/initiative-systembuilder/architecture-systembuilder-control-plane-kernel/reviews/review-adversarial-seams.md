# Adversarial Seam Review — SystemBuilder SaaS

**Verdict:** **Changes required before finalization.** The ADs establish the right ownership boundaries, but five cross-unit protocols remain underspecified. In each case below, two independently built units can follow every current AD and still disagree at a handoff, duplicate a material effect, or accept a result which is no longer valid. The items need a binding invariant or an explicitly blocking pre-implementation contract; implementation detail may still be deferred.

## Method

For each seam, the review assumes independent teams implement the named units in good faith and obey all `AD-1`–`AD-12`, including: Laravel owns governed state, scope is server-derived, runners are untrusted, adapters are port-bound, Redis is coordination only, and consumers are correlation-id idempotent. A finding exists only if incompatibility nevertheless remains possible.

## Findings

### AS-01 — High — Governed mutation and Redis dispatch lack one atomic handoff

**Independently-built pair:** Laravel Workflow/Execution command handler ↔ Laravel Queue/Horizon + Runner Manager dispatcher.

The command handler can append an `ExecutionAuthorized` Ledger fact and commit governed state, then fail before enqueueing the Redis job. Another correct implementation can enqueue before transaction commit and let a worker observe a job whose authorization rolls back. Both respect AD-3, AD-5, AD-6, AD-7, AD-9, and the atomic Ledger/projection convention; none requires a durable state-to-dispatch handoff.

**Failure:** a durable authorized execution never runs, or an uncommitted/revoked execution reaches Runner Manager. Manual requeue then risks duplicate execution.

**Required reconciliation:** define a Control Plane-owned transactional-outbox (or equivalent durable dispatcher) boundary: the authorization/transition and an immutable dispatch intent commit together; a worker publishes the intent at-least-once; Runner Manager accepts it only once by execution-attempt identity. State the owner and terminal cancellation/revocation behavior. The exact queue payload and retry numbers may remain Deferred, but this guarantee may not.

### AS-02 — High — Execution attempts, late runner output, and stage transitions have no concurrency/precedence contract

**Independently-built pair:** Gate/Workflow transition module ↔ Runner result-ingestion module.

Both can invoke Laravel application commands and validate correlation/policy as AD-3 and AD-7 require. One can approve a Gate, cancel an Execution, or start a replacement attempt while the other accepts output from the original runner. Correlation-id idempotency prevents an identical message twice; it does not decide whether a *different*, late but authentic message may mutate an aggregate after cancellation, supersession, profile revision, or failed downstream impact.

**Failure:** a stale runner result is accepted or a Gate/Stage appears to advance after its execution is cancelled/superseded; alternate consumers can apply different terminal-state precedence.

**Required reconciliation:** make the Execution Attempt a versioned, single-writer governed aggregate with an explicit state machine, attempt number/fencing token, expected workflow/profile/Constitution/base-context revisions, and transition precedence. Laravel must reject results that are not from the active authorized attempt; terminal state decisions and retry/supersede semantics must be visible in the runner contract.

### AS-03 — High — Git delivery has no compare-and-swap acceptance/merge rule

**Independently-built pair:** Runner/Git Provider adapter ↔ Workflow/Gate and Deployment modules.

AD-10 correctly scopes a runner to an isolated repository and per-execution branch/credential, but it does not bind the expected base commit, protected merge authority, or the condition under which Laravel treats a commit/diff as the accepted output of an execution. Two runners can legitimately create branches from the same base; a Git adapter may merge either to the target branch while the Workflow module accepts the other’s artifacts and gates. This is compatible with every current AD.

**Failure:** accepted requirements/evidence refer to a commit that is no longer (or never was) the delivery branch content, or a valid but stale execution overwrites a newer change.

**Required reconciliation:** bind a Git execution contract: record immutable expected base commit and output commit/tree hash; require a Control Plane-authorized compare-and-swap/protected-merge command after the relevant gates pass; reject/rebase/re-execute on branch-head conflict. Deployment Runs must cite the accepted immutable commit, not a mutable branch name.

### AS-04 — High — The BFF-to-Laravel principal and scope assertion are not a contract

**Independently-built pair:** Next.js session/BFF ↔ Laravel HTTP/API authorization adapter.

AD-2 says BFF uses an opaque secure cookie and Laravel re-derives identity/Tenant/Project/action scope; AD-4 prohibits unverified browser values. Neither AD specifies how Laravel receives a verifiable principal for a server-to-server BFF call, how switching project is represented, how service identity differs from the end-user actor, or how expiration/revocation is checked. One compliant BFF may forward a signed short-lived assertion, while another Laravel adapter expects an internal session lookup or uses a trusted service header with actor metadata.

**Failure:** valid users cannot be reauthorized across the boundary, audit actor/origin becomes ambiguous, or a service-to-service credential is accidentally treated as authority to choose a tenant/project.

**Required reconciliation:** define a versioned internal principal-delegation contract before either boundary is implemented: issuer/audience, actor vs service identity, expiry/revocation, allowed request correlation, and the rule that Laravel resolves membership and target scope from verified identity plus the requested resource/action—not BFF-supplied scope claims alone. Keep the identity-provider choice Deferred.

### AS-05 — Medium — Progress and provider-usage events lack canonical envelopes and ownership at ingestion

**Independently-built pair:** Runner Manager/provider adapters ↔ Laravel event/usage ingestion ↔ BFF streaming adapter.

AD-8 says the Control Plane owns versioned usage/cost records, and AD-9 confines browser progress to BFF-mediated SSE/WebSocket. It does not bind event producer identity, event/attempt sequence, usage measurement source, deduplication key, terminal ordering, or the split between non-authoritative progress and governed result. A provider adapter and Runner Manager can both report valid usage for the same execution, while the BFF can display an earlier “running” event after a governed terminal decision.

**Failure:** duplicate/misattributed usage records or stale Console progress; both cause operational/cost and user-trust errors without violating provider fallback or state-authority rules.

**Required reconciliation:** define a versioned execution-event envelope with execution attempt/fencing identity, producer, monotonically ordered sequence (or explicit unordered semantics), occurred-at, correlation and idempotency keys. Laravel ingests and deduplicates the authoritative usage record; BFF streams a read projection and treats terminal governed state as authoritative. The selected transport and provider-specific normalization detail can remain Deferred.

## Seams that the current spine closes adequately

- Direct browser access to queues/runners and raw secrets is prohibited by AD-2, AD-7, AD-8, and AD-9.
- Runner access to MySQL, Redis, global object-storage credentials, host Docker, or other project workspaces is prohibited by AD-7 and AD-9.
- Shared-schema scope loss is addressed by server-derived Tenant/Project context, fail-closed repositories, and execution/object/Git scoping in AD-4.
- A runner’s successful command cannot itself advance governed workflow state, because Laravel accepts/rejects/quarantines untrusted output under AD-3 and AD-7.

