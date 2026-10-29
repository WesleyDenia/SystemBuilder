---
type: epic
title: "Ledger, traceability and asynchronous integrity substrate"
parent: initiative-systembuilder
covers: [CAP-8, CAP-21]
after: [epic-tenant-authority-foundation]
assignee: ""
risk: high
---

# Ledger, traceability and asynchronous integrity substrate

## Description

Entrega o substrato durável que torna cada mutação governada atómica, sequenciada e reconstruível, antes de o kernel de Workflow criar factos de domínio.

## Outcome

Factos governados e mensagens assíncronas permanecem auditáveis e rejeitam replay, duplicação ou transição inválida em componentes compatíveis de produção.

## Requirements

- CAP-8: reconstruir autorizações, versões, Evidence, risco e impacto sem logs brutos.
- CAP-21: persistir Ledger e outbox atomicamente e rejeitar sequência ou transição inválida.

## Done when

1. A transação concreta grava Ledger e outbox atomicamente em MySQL, com Redis limitado a coordenação/fila.
2. O envelope canónico preserva evento, correlação e sequência; consumidores deduplicam e rejeitam replay e transições inválidas.
3. Projeções autorizadas permitem começar de Requirement, Decision, Baseline, Artifact, Evidence, Gate ou Finding sem expor logs brutos.
4. Falhas de dispatcher ou consumidor não convertem resultado candidato em estado aceite.
5. O substrato é entregável em produção compatível quando OQ-4/OQ-5 definirem a operação aplicável.

## Boundaries

Implementa persistência, outbox, envelopes e projeções sanitizadas; não decide lifecycle, Gate, Capability ou aceitação de resultados.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-8 and CAP-21
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-8, CAP-21 and Constraints
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-5 and AD-13
