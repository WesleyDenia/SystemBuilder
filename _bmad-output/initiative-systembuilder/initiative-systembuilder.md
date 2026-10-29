---
tracker_id: "1"
remote: "https://github.com/WesleyDenia/SystemBuilder/milestone/1"
type: initiative
title: "SystemBuilder — SaaS Control Plane Kernel"
parent: none
covers: [CAP-1, CAP-2, CAP-3, CAP-4, CAP-5, CAP-6, CAP-7, CAP-8, CAP-9, CAP-10, CAP-11, CAP-12, CAP-13, CAP-14, CAP-15, CAP-16, CAP-17, CAP-18, CAP-19, CAP-20, CAP-21]
after: []
assignee: ""
risk: high
---

# SystemBuilder — SaaS Control Plane Kernel

## Description

SystemBuilder governa trabalho de desenvolvimento assistido por IA para cada Tenant e Project sem se tornar um agente de geração. A iniciativa entrega contratos versionados, decisões auditáveis e execução isolada, para que apenas trabalho autorizado avance e cada decisão possa ser reconstruída.

## Outcome

Operadores internos conseguem governar, executar e auditar trabalho de Project com fronteiras de Tenant/Project, Gate e Evidence verificáveis; a demonstração candidata à release definida na SPEC é o sinal de sucesso.

## Requirements

O contrato numerado canónico é [SPEC-systembuilder-control-plane-kernel](spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md), secção **Capabilities**. Os CAP-1 a CAP-21 acima são a fonte estável dos requisitos atribuídos aos epics.

## Done when

1. As capacidades CAP-1 a CAP-21 têm entrega governada e verificável, ou a sua dependência aberta está explicitamente registada e impede a ativação indevida.
2. O caminho autenticado de Stage, Readiness, autorização, execução, Artifact e Evidence mantém isolamento de Tenant/Project e não permite fecho por sucesso técnico.
3. Toda mutação material produz Ledger e outbox atómicos; a auditoria reconstrói a decisão sem expor logs brutos ou segredos.
4. O Console autoriza ações apenas através do BFF/Laravel e cumpre o modo de consulta segura em mobile, acessibilidade, teclado, quatro locales e Evidence sanitizada.
5. Um Deployment Run de referência demonstra aprovações, segredo escopado, health check e rollback sem partilhar a pipeline ou credenciais do SaaS.

## Boundaries

Esta iniciativa segue as fronteiras Control Plane Laravel, Console/BFF Next.js e Execution Plane isolado. Não escolhe IdP, provider/model/executor, runtime de runner, região, retenção, CI/IaC, SLO ou provider de deployment enquanto as Open Questions correspondentes não forem decididas.

- Touch point: configuração e seams de plataforma — base e enforcement de persistence-repository; owner: epic-tenant-authority-foundation.
- Touch point: MySQL, Redis e envelopes duráveis — ledger/outbox e coordenação; owner: epic-ledger-traceability-integrity.
- Touch point: rede privada, storage, backup e restore — evidência de prontidão operacional; owner: epic-isolated-execution-artifacts-context.
- Touch point: design system e UX — foundation no primeiro epic e superfícies operacionais no Console; owner: epic-governed-console-audit.
- Touch point: deployment do SaaS — fora do escopo de Deployment Runs de Project de cliente.

Tracer path: uma ação autenticada e scoped passa pelo BFF e Control Plane, persiste um facto atómico, avalia Readiness, autoriza uma Execution, aceita Artifact isolado e apresenta Evidence/auditoria sanitizada; um Deployment Run reutiliza apenas os resultados governados.

## References

- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, Capabilities and Constraints
- prd — _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, Requirements and Exit
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-1–AD-13
- ux — _bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/EXPERIENCE.md, Foundation and user journeys

## Notes

- Decision: initiative split into seven epics in the recorded build order (user confirmation, 2026-10-28).
- Open question: OQ-1 is owned by epic-tenant-authority-foundation; OQ-2 by epic-capability-execution-authorization; OQ-3–OQ-6 by epic-isolated-execution-artifacts-context; OQ-7 by epic-governed-workflow-kernel; OQ-8–OQ-9 by epic-client-deployment-governance.
- Decision: OQ-4 and OQ-5 block activation or release in production, not completion of compatible implementation work in an epic (user-confirmed initiative split, 2026-10-28).
