---
type: epic
title: "Governed client deployment runs"
parent: initiative-systembuilder
covers: [CAP-15, CAP-20]
after: [epic-governed-console-audit]
assignee: ""
risk: high
---

# Governed client deployment runs

## Description

Governa delivery de Project de cliente como Deployment Run independente, usando um adapter homologado e primitivas de Console/BFF existentes sem misturar deployment do SaaS ou credenciais de runner.

## Outcome

Um operador aprova, acompanha, verifica e faz rollback de uma entrega de cliente com segredos e ambientes escopados, deixando rastreabilidade durável.

## Requirements

- CAP-15: credenciais e objetos de Deployment Run são derivados de scope e rejeitam acesso cross-Tenant/cross-Project.
- CAP-20: Gates, ambientes, secrets, aprovações, release, health check e rollback são correlacionados sem alterar automaticamente a Baseline aceite.

## Done when

1. Deployment Run declara Gate, ambiente, approval, release, health check e rollback como lifecycle separado do SaaS.
2. Segredos por ambiente são temporários, escopados e ausentes de browser, Ledger, Evidence, prompts e logs.
3. O adapter selecionado cumpre a capability policy do epic de autorização e rejeita provider ausente sem fallback.
4. Console/BFF reutiliza primitivas existentes para aprovação, monitorização e rollback sem competir com rotas do epic Console.
5. A referência de delivery é entregável em produção após OQ-8; não usa pipeline SaaS, credencial de runner nem atualiza Baseline automaticamente.

## Boundaries

Trata delivery de Project de cliente e o adapter selecionado. Não implementa deployment/operação do próprio SystemBuilder SaaS, nem escolhe provider antes de OQ-8.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-15 and CAP-20
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-20 and Constraints
- prd — _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, FR-17 and NFR-8
- ux — _bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/EXPERIENCE.md, UJ-6

## Notes

- Open question: OQ-8 escolhe o primeiro adapter/provider de deployment; OQ-9 define limites comerciais futuros e não bloqueia o lifecycle interno.
