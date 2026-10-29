---
type: epic
title: "Governed workflow, assurance, profiles and readiness"
parent: initiative-systembuilder
covers: [CAP-1, CAP-2, CAP-3, CAP-4, CAP-5, CAP-6, CAP-7, CAP-10, CAP-11, CAP-13, CAP-14]
after: [epic-ledger-traceability-integrity]
assignee: ""
risk: high
---

# Governed workflow, assurance, profiles and readiness

## Description

Modela o contrato e lifecycle versionados que decidem a ação elegível, preservam Assurance, Gate, Evidence, exceções e impacto, usando Ledger/outbox em cada mutação.

## Outcome

Uma Stage só avança por Gate e Exit Contract declarados, e os perfis iniciais demonstram conformance de lifecycle, Readiness, Evidence e fecho em produção compatível.

## Requirements

- CAP-1–CAP-3: Stage Contract, Readiness e tratamento auditável de Override/Exceção Formal.
- CAP-4–CAP-7: Assurance, Baseline/impact e Workflow declarativo independente de UI ou provider.
- CAP-10–CAP-11: perfis iniciais e Perfil BMAD não bypassável.
- CAP-13–CAP-14: adoção de Input/Artifact e dimensões independentes de Interaction Mode e Autonomy Policy.

## Done when

1. Stage Contract, Workflow, Stage Revision, Baseline, Gate e Exit Contract são versionados e append-oriented.
2. PASS, CONCERNS, FAIL e BLOCKED produzem próxima ação e Evidence; BLOCKED nunca recebe bypass genérico e exceções preservam o resultado original.
3. Assurance Records obrigatórios começam em Ideia/Discovery/Product Brief e impedem fecho quando incompletos.
4. Ideia/Discovery/Product Brief, PRD e Coding/Story passam conformance de lifecycle, Readiness, Evidence e Exit Contract; Coding/Story falha sem PRD, UX, Architecture e Project Context válidos.
5. Reabertura, impacto, adoção e autonomia preservam proveniência e elegibilidade, e o kernel é entregável em produção compatível após OQ-4.

## Boundaries

Decide semântica de governação e usa os contratos duráveis do epic anterior. Não implementa adapters, runner, object storage ou UI completa. O default Guided + Supervised permanece fixo até OQ-7.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-1–CAP-7 and CAP-10–CAP-14
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-1–CAP-7 and CAP-10–CAP-14
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-5 and AD-11

## Notes

- Open question: OQ-7 define a taxonomia configurável de Interaction Mode; não bloqueia o default Guided + Supervised.
