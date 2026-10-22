# Reconciliação — Fluxo de Desenvolvimento do Painel BMAD

Fonte: `docs/0005_01-Fluxo_Desenvolvimento_Painel_BMAD.pdf`

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

Regra aplicada: este fluxo é referência de produto anterior. O v0.6 prevalece para modos, sequência vigente e escopo.

## Cobertura forte

- O princípio “BMAD é motor; painel é governança e experiência” está no propósito, visão, PD-8/PD-9 e non-goals. Fonte: PDF pp. 1, 10.
- A estrutura previsível de cada fase — objetivo, inputs, workflow, Artifact, reviews, Gate, dependências e decisões — é generalizada em FR-2 e FR-7. Fonte: PDF p. 2.
- Ideation/Brainstorm, PRD e Coding/Story foram escolhidos exatamente como perfis conceitual, documental e de engenharia, com critérios específicos em FR-18 a FR-20. Fonte: PDF pp. 2–6.
- Story Readiness, gates pré/pós-implementação, build/test/code review/security review e conclusão não baseada apenas em código gerado aparecem em FR-20. Fonte: PDF pp. 5–6, 10.
- Findings de segurança ligados ao Artifact, com severidade/risco/recomendação/estado e blocking behavior, estão cobertos por Finding, Gate, FR-5/FR-6 e FR-20. Fonte: PDF p. 8.
- IDs estáveis de Requirement e links para UX, Story, commit, test e release foram generalizados em FR-12 e no Stage Profile PRD/Coding. Fonte: PDF p. 7.
- O histórico compreensível sem leitura de logs brutos está no resultado pretendido, UJ-4, FR-12, NFR-2 e NFR-10. Fonte: PDF p. 8.
- Baseline, ADR/decisão e guardrails preservados sem alteração silenciosa são representados por Baseline, Decision, versionamento e ledger append-oriented. Fonte: PDF pp. 4, 7–8.
- O addendum preserva a tensão correta entre progressive disclosure e visão completa de auditoria.

## Gaps materiais

1. **Context package/Project Constitution não está explícito.** A fonte exige que cada agente receba regras duradouras + contexto mínimo necessário. FR-8 diz “apenas contexto necessário”, mas não exige pacote versionado contendo decisões/ADRs, baseline de segurança, guardrails, dependências e práticas proibidas. Fonte: PDF p. 8.
2. **A proveniência de guardrails derivados de retrospectiva não está modelada.** Ledger e versionamento comportam isso, mas não há requisito que ligue learning/retrospective → guardrail versionado → Execution futura. Para este slice, pode ser uma extensão declarada, não implementação. Fonte: PDF p. 7.
3. **Traceability mínima por perfil pode ficar permissiva.** FR-12 aceita links tipados, porém não declara quais anchors são obrigatórios no fechamento de PRD e Coding/Story além de formulações gerais. A fonte exige Requirement IDs, Story→Requirement/UX, Test→Story e Release→Requirements/Stories. Fonte: PDF p. 7.
4. **UX operacional dos gates está somente como OQ-15/addendum.** A fonte requer destacar gates, pendências, decisões, bloqueios e histórico sem expor logs. Mesmo sem definir UI final, faltam outputs/query capabilities voltadas a uma apresentação progressiva. Fonte: PDF p. 8.

## Conflitos e resolução

- **Modos:** a referência usa Guiado/Colaborativo/Especialista; v0.6 confirma Guiado/Avançado e separa modo de autonomia. O PRD deve seguir v0.6 e não reintroduzir os três modos antigos.
- **Sequência de etapas:** o documento antigo usa Ideia→Descoberta→Brief/Brainstorm→PRD→UX→Arquitetura...; v0.6 expande/reordena o ciclo. Este Kernel não deve codificar nenhuma sequência completa; Stage/Workflow declarativos são a resolução correta.
- **Epic como uma Sprint:** a regra operacional da referência não foi confirmada no v0.6 e não pertence a este slice. Não adotar.
- **Finding bloqueante “formalmente tratado”:** deve ser interpretado segundo o v0.6 posterior: `BLOCKED` não tem bypass genérico; somente exceção formal específica previamente definida. FR-5/FR-6 refletem a prioridade correta.
- **UX antes de arquitetura:** é decisão de fluxo anterior e não afeta o Kernel genérico; não promover a regra no PRD.

## Correções recomendadas

- Definir, no contrato Control/Execution Plane ou Stage Contract, um `governed_context_package` versionado e mínimo, sem exigir uma feature completa de Project Constitution neste slice.
- Reservar Traceability Link e versionamento para `derives_guardrail_from`/equivalente e registrar a versão de guardrail usada por uma Execution.
- Tornar obrigatórios no Exit Contract de cada perfil os anchors mínimos aplicáveis; para Coding/Story, ao menos Story→Requirement, Execution→diff/commit e Test/Finding→Story/Artifact quando existentes.
- Transformar OQ-15 em requisito de projeção/consulta: fornecer resumo de status, gaps, Gate, Decision e impacto adequado a progressive disclosure, deixando o design visual para UX posterior.

