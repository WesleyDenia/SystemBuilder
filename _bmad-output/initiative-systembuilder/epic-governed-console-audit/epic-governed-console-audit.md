---
type: epic
title: "Governed Console, Evidence and traceability"
parent: initiative-systembuilder
covers: [CAP-8, CAP-16]
after: [epic-isolated-execution-artifacts-context]
assignee: ""
risk: high
---

# Governed Console, Evidence and traceability

## Description

Entrega a superfície operacional não relacionada a Deployment Run: o Console/BFF mostra contexto, Readiness, ação elegível, Evidence e rastreabilidade autorizadas sem inferir autoridade no browser.

## Outcome

Um operador autorizado consegue operar e auditar trabalho governado no desktop e consultar com segurança em mobile, com explicações textuais, acessíveis e sanitizadas.

## Requirements

- CAP-8: apresenta a projeção autorizada de rastreabilidade; o epic Ledger mantém os factos imutáveis e este controla a apresentação sanitizada.
- CAP-16: Console/BFF reautorizado, WCAG 2.2 AA, teclado, quatro locales, tema, ações críticas desktop/laptop e mobile somente consulta.

## Done when

1. Desktop mostra Stage, Readiness, razões, ação elegível, Evidence e auditoria apenas através de BFF/Laravel reautorizado.
2. Evidence, previews e URLs de Artifact são classificados, redigidos e limitados a objeto/ação autorizados; não expõem segredos ou logs brutos.
3. Revision, impacto e traceability são navegáveis a partir das projeções do Ledger com estado, regra, razão e risco em texto.
4. WCAG 2.2 AA, teclado, pt-PT, pt-BR, en, es, tema e mobile de consulta segura são verificados; UI não concede autoridade.
5. A superfície é entregável em produção compatível; rotas de Deployment Run ficam no epic seguinte.

## Boundaries

Implementa presentation/read models e adaptadores de ação do Console para workflow já autorizado. Não decide Gate, política, Capability, runner ou rotas de aprovação/rollback de Deployment Run.

## References

- parent — _bmad-output/initiative-systembuilder/initiative-systembuilder.md, CAP-8 and CAP-16
- spec — _bmad-output/initiative-systembuilder/spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md, CAP-8, CAP-16 and Constraints
- ux — _bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/EXPERIENCE.md, Foundation and UJ-1–UJ-5
- design — _bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/DESIGN.md, visual and component rules
