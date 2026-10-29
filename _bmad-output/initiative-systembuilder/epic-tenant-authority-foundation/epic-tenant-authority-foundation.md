---
tracker_id: "2"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/2"
tracker_status: backlog
type: epic
title: "Tenant authority and platform foundation"
parent: initiative-systembuilder
covers: [CAP-15, CAP-16]
after: []
assignee: ""
risk: high
---

# Tenant authority and platform foundation

## Description

Estabelece o esqueleto hexagonal do Control Plane e a fronteira Console/BFF, com scope de Tenant e Project derivado de membership. Não existem mutações governadas antes de o substrato Ledger/outbox estar disponível.

## Outcome

Cada pedido começa com contexto autorizado e falha fechadamente fora do scope, dando aos restantes epics uma fundação verificável para componentes compatíveis de produção.

## Requirements

- CAP-15: derivar Tenant, Project e ação de membership autenticada em todas as fronteiras, sem revelar recursos fora do scope.
- CAP-16: fundação de sessão, fronteira browser/BFF e bootstrap visual; o epic Console entrega as superfícies, read models, Evidence/auditoria, WCAG, locales e mobile de consulta.

## Done when

1. O Control Plane tem módulos Domain, Application e Infrastructure, e o bootstrap de sessão, BFF e design system permite ao Console falar apenas com o BFF.
2. Scope por membership e reautorização falham fechadamente em persistência e BFF, com testes cross-Tenant/cross-Project e de não divulgação.
3. Browser não acede diretamente a Laravel, Redis, Runner Manager ou runner; a única exceção é URL pré-assinada, curta e autorizada para um objeto/ação.
4. Existe um seam de transação de aplicação, mas nenhuma mutação governada é aceite antes do epic Ledger fornecer outbox atómico.
5. O componente é entregável em produção compatível quando as decisões operacionais de OQ-4 forem aplicadas.

## Boundaries

É a fundação de membership, sessão, BFF, bootstrap visual e enforcement de scope em persistence repositories. Não implementa lifecycle, Ledger/outbox concreto, Git/ref, object access, callbacks, runner nem as superfícies operacionais completas de CAP-16.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-15 and CAP-16
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-15, CAP-16 and Constraints
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-1–AD-4
- ux — _bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/EXPERIENCE.md, Foundation

## Notes

- Open question: OQ-1 decide IdP, MFA e autorização antes de onboarding externo; não altera a obrigação de derivar scope no servidor.
- Decision: the first story is the cross-layer tracer and establishes canonical verification commands; OQ-1 is the human-owned second story that gates concrete identity integration (user confirmation, 2026-10-29).
- Decision: scoped persistence and BFF Console work may run in parallel after the application scope boundary; direct-object policy follows both (user confirmation, 2026-10-29).
- Decision: a refactor sweep and final end-to-end foundation conformance close this nine-story epic (user confirmation, 2026-10-29).
