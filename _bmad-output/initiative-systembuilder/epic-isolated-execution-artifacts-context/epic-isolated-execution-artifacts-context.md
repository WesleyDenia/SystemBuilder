---
type: epic
title: "Isolated execution, durable artifacts and Project Constitution"
parent: initiative-systembuilder
covers: [CAP-15, CAP-17, CAP-18, CAP-19]
after: [epic-capability-execution-authorization]
assignee: ""
risk: high
---

# Isolated execution, durable artifacts and Project Constitution

## Description

Executa uma tentativa autorizada em runner descartável e limitado, preservando apenas resultados duráveis, Git escopado e Constitution aprovada fora do workspace efémero.

## Outcome

Uma tentativa permitida não recebe segredos ou acesso excessivo, e os Artifacts e contexto aceites permanecem reconstruíveis depois da perda do runner.

## Requirements

- CAP-15: runner, Git e object access aplicam scope de Tenant/Project sem revelar recursos externos.
- CAP-17: runner rootless, limitado e com credenciais temporárias estritamente escopadas.
- CAP-18: Git/ref e Artifact durável usam metadata governada, referência ou hash e compare-and-swap.
- CAP-19: Constitution versionada mínima é selecionada e mudanças materiais revalidam Execution.

## Done when

1. Runners rootless e resource-limited negam MySQL, Redis, Docker socket, credenciais globais, segredos de outros providers e acesso cross-runner.
2. Credenciais temporárias de Git/ref e object access são escopadas; updates CAS e objetos fora do scope são negados.
3. Artifact aceite conserva metadata, hash/referência e Provenance duráveis; perda do workspace não destrói a evidência.
4. Cada Execution seleciona uma revisão aprovada de Constitution e mudança material revalida a autorização.
5. A conformance base é entregável em produção compatível; runner real requer OQ-3, rede/storage/backup/restore/migrations aguardam OQ-4/OQ-5 e hardening adicional só é exigido pelo gatilho de OQ-6.

## Boundaries

É o Execution Plane, Artifact/Git e Constitution. Consome a autorização anterior, mas não decide capability, Gate ou aceitação de Stage; não escolhe runtime, região ou tecnologia de hardening em aberto.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-15 and CAP-17–CAP-19
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-17–CAP-19 and Constraints
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-6, AD-7, AD-9 and AD-10

## Notes

- Open question: OQ-3 define runtime/protocolo do runner; OQ-4/OQ-5 definem operação, dados e produção; OQ-6 define gatilho de sandboxing reforçado.
