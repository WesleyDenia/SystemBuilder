---
title: 'Restore governance contract traceability'
type: 'chore'
ticket: ''
created: '2026-10-29'
status: 'built'
baseline_revision: 'ef04fd6a57c80e02d8194700bfb1ee0f9a662266'
route: 'full'
route_source: 'auto'
review: 'quick'
review_source: 'pinned'
lenses_ran: [quick]
review_loop_iteration: 0
context:
  - 'AGENTS.md'
  - '_bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md'
  - '_bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** The uncommitted SaaS-scope re-derivation weakens traceability: it removes authoritative sources from the SPEC, silently repurposes historical open-question IDs, and asserts that no ticket tree exists. Several approved control details are present in the source contracts but are not sufficiently connected to the updated SPEC, making implementation and acceptance ambiguous.

**Approach:** Correct the canonical SPEC and its companions as an append-oriented governance revision. Preserve approved Kernel capability IDs, document CAP-15–CAP-21 as additive scope with explicit release conditions, retain historical question identity, and record the actual impact on the existing initiative ticket tree without altering any ticket.

## Boundaries & Constraints

**Always:** Treat PRD, Architecture Spine, UX, and `AGENTS.md` as normative sources; preserve immutable history and stable IDs; keep Laravel as the sole governed-state authority; keep runner and Evidence controls aligned with the approved contracts; distinguish an approved invariant from an unresolved concrete implementation choice.

**Never:** Change source application code, ticket bodies, ticket status, GitHub state, the deferred ticketing-configuration item, or select a provider, runtime, deployment adapter, IAM model, retention policy, or production infrastructure choice.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|---------------|---------------------------|----------------|
| Source conflict | PRD, architecture, UX, or context disagree | SPEC declares precedence and requires an explicit revision before a contradiction becomes accepted | No local companion silently overrides a normative source |
| Historical OQ reference | Existing reference to a pre-revision OQ | Same ID retains its original subject and disposition, or a durable migration points to its successor | New uncertainties receive fresh IDs rather than reusing old ones |
| Deployment verification | OQ-25 remains unresolved | Deployment Run remains an approved capability, but reference delivery is a future gated criterion | No release claim says an unselected adapter is already proven |
| Ticket impact | Existing initiative and epic `tickets.toml` files | Impact companion inventories relevant epics and says whether ticket bodies changed | No assertion that the ticket tree is absent |

**Decision (2026-10-29):** Preserve historical OQ identity: OQ-8 remains the first BMAD homologation question. OQ-25 owns Deployment Provider Adapter selection; add a durable transition table for SaaS references that previously used OQ-1–OQ-9.

</frozen-after-approval>

## Code Map

- `_bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md` -- canonical contract; restore `sources`, define source precedence, express the scope revision, link concrete controls to approved references, and qualify the delivery signal.
- `_bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/open-questions.md` -- current OQ register; preserve the HEAD-era OQ identities/dispositions and allocate fresh IDs to newly derived questions.
- `_bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/requirements-and-ticket-impact.md` -- current CAP coverage and future slicing; replace the false empty-tree assertion with a verified impact inventory.
- `_bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/.memlog.md` -- append-only provenance; record the scope revision and true ticket-impact evidence without rewriting earlier facts.
- `_bmad-output/initiative-systembuilder/tickets.toml` and `epic-*/tickets.toml` -- read-only evidence for the initiative/epic inventory; do not modify.
- `AGENTS.md`, PRD FR-8–FR-12/FR-17 and Architecture AD-6–AD-10/AD-13 -- approved constraints to cite rather than duplicate.

## Tasks & Acceptance

**Execution:**
- [ ] `spec-systembuilder-control-plane-kernel.md` -- reinstate normative sources, specify their precedence and revision rule, label CAP-15–CAP-21 as additive approved scope, reference the approved runner/artifact/async/Evidence rules, and make the Deployment Run demonstration conditional on OQ-8 -- prevents local prose from silently widening or weakening the contract.
- [ ] `open-questions.md` -- restore stable historic OQ subjects and dispositions; retain valid new uncertainty as fresh IDs with owners/triggers; separate resolved invariants from implementation choices -- preserves auditability and avoids false closure.
- [ ] `requirements-and-ticket-impact.md` -- inventory the existing seven epic breakdowns and their CAP coverage, state that ticket bodies are not modified, and retain required Evidence/test expectations -- gives builders a truthful migration impact record.
- [ ] `.memlog.md` -- append a dated correction event describing the explicit scope revision and verified ticket-tree evidence -- maintains append-only provenance.
- [ ] affected spec companion files -- run structural checks and targeted content searches for source authority, OQ continuity, ticket-tree references, containment, integrity, async acceptance, Evidence handling, and conditional deployment language -- catches stale or contradictory claims.

**Acceptance Criteria:**
- Given the canonical SPEC, when a builder resolves a conflict, then it can identify authoritative sources, their precedence, and the required explicit-revision path.
- Given a historical OQ reference, when it is read after this revision, then its subject and disposition remain traceable and no unresolved design choice is silently marked resolved.
- Given the unchanged initiative ticket tree, when ticket impact is reviewed, then the document names the extant epic coverage and does not claim the tree or published tickets are absent.
- Given OQ-8 is open, when the success signal is evaluated, then it cannot be used to claim a production/reference deployment adapter has already been verified.
- Given runner, artifact, asynchronous, or restricted-Evidence work is planned, when the SPEC is used, then the approved source contracts provide the binding controls without duplicating or contradicting them.

## Implementation Notes

## Plan Change Log

- 2026-10-29 — Review found the frozen matrix incorrectly assigned deployment selection to OQ-8. The human confirmed historical identity wins: the matrix now names OQ-25 and the implementation must add a transition table for references produced during the SaaS re-derivation. This avoids reusing OQ-8 while preserving traceability for already-written SaaS references. **KEEP:** retain the restored historic OQ register, explicit source authority, seven-epic impact inventory, and conditional deployment criterion.

## Review Triage Log

- 2026-10-29 — **intent_gap** — `spec-systembuilder-control-plane-kernel.md:161`: o plano congelado usa OQ-8 como condição para o adapter de deployment, mas a correção de continuidade devolve OQ-8 à homologação BMAD e usa OQ-25 para deployment. O contrato atual satisfaz a continuidade histórica; é necessária uma decisão humana para resolver a referência OQ-8 congelada antes de nova derivação.
- 2026-10-29 — **patch** — `open-questions.md:7-36`: referências SaaS transitórias que ainda usam os números reatribuídos precisam de uma tabela de migração para OQ-18–OQ-26; a intenção já exige rastreabilidade sem mutar tickets.
- 2026-10-29 — **false** — `_bmad/custom/ticketing-store-config.toml` e `deferred-work.md`: o ficheiro de configuração já existia antes desta implementação e não foi modificado; o item diferido foi criado pelo workflow no momento em que o utilizador dividiu o escopo. Nenhum deles é uma mutação desta correção de contrato.
- 2026-10-29 — **resolved after human decision** — a matriz e o contrato agora tratam OQ-25 como seleção de Deployment Provider Adapter; `open-questions.md` preserva OQ-8 como homologação BMAD e inclui uma tabela de migração OQ-1–OQ-9 transitória para OQ-18–OQ-26. A rastreabilidade dos tickets existentes é preservada sem os modificar.

- `medium` / `patch` — `_bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md:30`: verificado que a Architecture Spine já aprova precedência sobre o PRD Kernel-only para conflitos de escopo arquitetural; a redação atual bloqueia indevidamente esse caso. Correção mínima em curso: declarar essa precedência, mantendo revisão explícita para as restantes contradições.
- `medium` / `rejected-out-of-scope` — `_bmad/custom/ticketing-store-config.toml:1`: o ficheiro não foi criado nem alterado por esta implementação de contrato e está fora do Code Map; o intent congelado proíbe alterar o item de configuração de ticketing adiado. Não foi modificado nem registado como trabalho diferido por esta correção.

- `medium` / `patch` — `spec-systembuilder-control-plane-kernel/.memlog.md:86`: confirmado que um facto histórico afirma incorretamente que não existe `tickets.toml`; a entrada posterior comprova a árvore, mas não identifica a afirmação antiga como errada. Acrescentar evento de correção append-only que preserve o facto e invalide explicitamente essa alegação.
- `medium` / `patch` — `spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md:130-141`: confirmado que as Constraints locais repetem controlos detalhados já vinculativos no PRD, Spine e `AGENTS.md`, apesar de o plano exigir citação por referência. Substituir essa cópia por uma referência concisa às fontes e manter os controlos concretos apenas na secção de referências aprovadas.

## Design Notes

The changes are a contract correction, not an architectural redesign. The SPEC stays concise by making the authoritative sources explicit and linking to existing PRD/Architecture/UX detail. New questions use fresh identifiers; prior identifiers remain stable even if their later implementation tracking moves to a new OQ.

## Verification

**Commands:**
- `git diff --check` -- expected: no whitespace errors.
- `rg -n "sources:|companions:|precedence|OQ-[0-9]+|tickets\.toml|Deployment Run|CAP-1[5-9]|CAP-2[0-1]" _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel` -- expected: source authority, OQ continuity, ticket impact, and conditional deployment wording are present and internally consistent.
- `uv run _bmad/method/scripts/tickets.py --project-root /home/oem/Workspace/SystemBuilder status` -- expected: reports the existing initiative tree without modifying it.

**Manual checks (if no CLI):**
- Compare every restored/renumbered OQ against the committed pre-revision file and confirm the new record preserves its original subject or records an explicit disposition.
- Read the CAP-to-epic impact table beside the initiative and each epic breakdown; confirm the companion makes no ticket mutation and does not overstate completed verification.
