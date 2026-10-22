# Open Questions

## Necessárias antes da implementação afetada

- **OQ-1 — Identidade/cardinalidade:** quais IDs, cardinalidades e ownership ligam Stage, Stage Revision, Workflow, Execution, Artifact, Evidence, Decision, Gate e Finding?
- **OQ-3 — Persistência/imutabilidade:** que mecanismos de versionamento, retenção, exportação, redaction e recuperação sustentam Ledger e Baseline?
- **OQ-5 — Control/Execution:** quais mensagens, falhas, idempotência, revogação e entrega de Evidence compõem a fronteira mínima?
- **OQ-6 — Secrets/contexto:** como credenciais, código, prompts, logs e Evidence são escopados, cifrados, redigidos, retidos e destruídos?
- **OQ-7 — Compatibilidade:** qual política de evolução vale para Contract, Profile, Workflow, Adapter e Matrix?
- **OQ-8 — BMAD alvo:** qual versão/tag/commit e inventário mínimo de operações/Contract Test entram no primeiro build?

## Deferíveis por interface explícita

- **OQ-9:** provider/executor inicial e Capabilities obrigatórias/opcionais.
- **OQ-10:** integração mínima de repo, branch/base, build, testes e reviews para Coding/Story real.
- **OQ-11:** Evidence, fixtures e checks adicionais mínimos por Stage Profile.
- **OQ-13:** SLOs e metas além das métricas de integridade após baseline instrumentada.
- **OQ-14:** `IMPORT_ADOPTION` é Gate próprio ou ação dentro de Entry Contract?
- **OQ-15:** interface inicial para pendências, Gate, histórico e impacto.
- **OQ-16:** beachhead/segmento/cenário inicial.
- **OQ-17:** requisitos de deploy, operação, observabilidade e suporte após o MVP.

Override de `FAIL`, Exceção Formal de `BLOCKED` e classificação determinística de impacto foram resolvidos como requisitos de produto em FR-4, FR-6 e FR-15; não são Open Questions nem decisões arquiteturais pendentes.
