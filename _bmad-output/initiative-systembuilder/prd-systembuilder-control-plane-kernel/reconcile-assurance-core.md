# Reconciliação — Assurance Core

Fonte reconciliada: `../spec-systembuilder-control-plane-kernel/assurance-core.md`.

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

## Cobertura forte

- PD-4, FR-10 e SM-1 tornam o Assurance Core obrigatório para toda Execution relevante desde Ideation/Brainstorm e impedem que registro incompleto sustente fechamento.
- FR-10 cobre quase todo o Minimum Record: Tenant/Project/Stage Revision/Execution, tempo, ator/origem, engine/versão, configuração/política, Inputs/outputs, decisões humanas, Gate, resultado, Evidence, Finding e risco residual.
- A profundidade crescente aparece em FR-18 a FR-20 e na regra de FR-10 de permitir acréscimos por perfil/risco sem remover campos mínimos.
- FR-4/FR-6 preservam a condição original e exigem risco residual para `CONCERNS` aceito e `FAIL` overrideable; FR-5/FR-20 tratam Findings bloqueantes. NFR-1 a NFR-3 preservam o histórico.

## Gaps

1. **A lista de campos não é isomórfica ao Minimum Record.** FR-10 não nomeia explicitamente `stage_id`, `workflow_id`, `run_id` nem início/fim ou event time; estes podem ser inferidos por relações, mas o aceite da fonte pede que cada registro os contenha ou referencie inequivocamente.
2. **“Campos mínimos aplicáveis” permite omissão ambígua.** Na fonte, identidade, workflow, run, timestamp, origem, engine/version, inputs/outputs, gates, result e evidence refs pertencem ao mínimo; somente alguns valores, como risco residual, são condicionais. O PRD não separa campos incondicionais de condicionais.
3. **A profundidade por perfil não está consolidada como requisito de Assurance.** Os dados aparecem dispersos nos Stage Profiles, mas faltam aceites que garantam: método/capability, decisões e concerns em Ideation; provenance de Requirement, assumptions abertas, approvals, anchors e impacto em PRD; branch/base commit, testes, reviews e diffs/commits em Coding/Story.
4. **As classes de risco que o kernel deve acomodar não estão refletidas em um requisito de extensibilidade.** A matriz de riscos do PRD cobre vários riscos de produto, mas não assegura que Findings/Assurance Record possam registrar hallucination, prompt injection, supply-chain risk, specification gaming, human complacency e business misalignment sem mudança de esquema incompatível.
5. **Referenciar Evidence sem duplicá-la não está normatizado.** O glossário modela Evidence e NFR-2 usa dados governados, mas falta o aceite explícito da fonte de que ledger/Assurance Record mantêm `evidence_refs`, não cópias repetidas do conteúdo em cada evento.

## Conflitos ou tensões

- Não há conflito material com a fonte.
- A expressão “campos mínimos aplicáveis” em FR-10 é uma tensão semântica: se interpretada amplamente, enfraquece o Minimum Record; se limitada apenas a campos condicionais, está alinhada.
- NFR-6 é deliberadamente mais estrito que esta fonte ao proibir secrets também em Artifact/Evidence. Isso é compatível como objetivo de segurança, mas exige redaction/classification na arquitetura e não deve ser tratado como uma regra derivada exclusivamente do Assurance Core.

## Correções recomendadas

1. Substituir o resumo de FR-10 por uma lista canônica de campos alinhada um-a-um ao Minimum Record, incluindo `stage_id`, `stage_revision`, `workflow_id`, `execution_id/run_id` e timestamps.
2. Marcar quais campos são sempre obrigatórios e quais são condicionais; exigir `residual_risk` para `CONCERNS` aceito e qualquer Override de `FAIL`.
3. Acrescentar uma tabela de profundidade por Stage Profile a FR-10 ou referências de aceite explícitas a FR-18/FR-19/FR-20.
4. Exigir taxonomia versionada/extensível de Finding/risk class que acomode as classes enumeradas sem afirmar que este MVP as detecta ou resolve.
5. Acrescentar a FR-10/FR-11 um aceite: Evidence é armazenada/referenciada por identidade e integridade; eventos e Assurance Records carregam refs, não duplicam o payload em cada linha.
