---
type: epic
title: "Capability governance and execution authorization"
parent: initiative-systembuilder
covers: [CAP-9, CAP-12, CAP-15]
after: [epic-governed-workflow-kernel]
assignee: ""
risk: high
---

# Capability governance and execution authorization

## Description

Declara combinações homologadas de BMAD, provider, modelo e executor, e autoriza apenas Executions correlacionadas e limitadas, sem fallback implícito.

## Outcome

Uma Execution só é autorizada por capability compatível e um resultado candidato continua em quarentena até o Control Plane validar política, correlação e sequência.

## Requirements

- CAP-9: Capability Matrix e adapters versionados com Contract Test e autoridade de Artifact declarada.
- CAP-12: autorização limitada, reconciliação e rejeição de execution expirada, incompatível ou não autorizada.
- CAP-15: jobs, callbacks e tokens de Execution derivam scope e não atravessam Tenant/Project.
- Credential Profile: material secreto é protegido fora de segredos mestre persistentes e só é materializado como credencial temporária escopada.

## Done when

1. Capability Matrix, Credential Profile, Model Policy e autoridade de Artifact são versionados e independentes; Credential Profile protege material secreto e só o injeta temporariamente no scope autorizado.
2. Metadata e canais de capability são redigidos de segredos; capability ausente ou incompatível falha explicitamente, sem fallback, e usage/cost só é registado quando a capability homologada o expõe.
3. Authorization tem correlação, vigência, revogação e scope verificáveis; jobs e callbacks cross-scope são negados e auditados.
4. Resultados candidatos divergentes, duplicados ou inválidos ficam em quarentena e sucesso técnico não fecha Stage.
5. Contratos genéricos de adapter e harness de Contract Test são entregáveis em produção compatível; a primeira combinação homologada depende de OQ-2.

## Boundaries

Entrega política de capability, segurança de Credential Profile e autorização do Control Plane. Não escolhe provider/model/executor, não constrói runtime de runner e não implementa o provider de Deployment Run; fornece a política genérica que esse provider terá de satisfazer.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-9, CAP-12 and CAP-15
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-9, CAP-12 and Constraints
- architecture — _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-7 and AD-8

## Notes

- Open question: OQ-2 decide a primeira combinação provider/model/executor e fixtures antes de Contract Test homologado.
