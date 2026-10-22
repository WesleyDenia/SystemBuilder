# Reconciliação: Workflow And Gates

Fonte reconciliada: `spec-systembuilder-control-plane-kernel/workflow-and-gates.md`  
Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`

## Cobertura forte

- O shape declarativo está coberto por FR-7 e pelo glossário de Workflow: Inputs, Artifact esperados, executor, provider/model policy, guardrails, reviewers, Gate, retry, timeout e transições são versionados e desacoplados de internals BMAD.
- Os seis tipos de Gate da fonte estão cobertos por FR-5: entrada/readiness, aprovação humana, qualidade, segurança, fechamento e impacto. FR-13 reforça o Exit Contract; FR-15 cobre impacto downstream.
- A política `PASS / CONCERNS / FAIL / BLOCKED` foi incorporada integralmente em PD-2, PD-3, FR-4 e FR-6, inclusive preservação do resultado original, risco residual e ausência de bypass genérico de `BLOCKED`.
- A separação entre Mode e Autonomy, o default `Guided + Supervised` e a proibição de inferir risco apenas por persona aparecem em PD-10, FR-7 e na tese comportamental do PRD.
- Os estados, a distinção entre `REQUIRES_REVIEW` e `STALE` e a reabertura versionada estão cobertos por FR-3, FR-14, FR-15 e A-6.
- O pipeline de Coding/Story está coberto no nível do recorte por FR-20: readiness antes do desenvolvimento, testes, code review, security review, verificação final e Exit Contract; geração de código não basta para concluir.

## Gaps

1. **Matriz canônica de transições não está preservada como invariante.** FR-3 exige transições declaradas, mas não explicita que os caminhos normais e de interrupção da fonte devem constituir o baseline do lifecycle. Um Workflow poderia, em tese, declarar uma rota incompatível e ainda satisfazer a redação atual.
2. **Políticas declarativas estão agregadas demais.** FR-7 não exige de forma inequívoca campos separados para `review_policy`, `security_policy` e `approval_policy`; menciona reviewers e Gate, mas a distinção é relevante para homologação, explicação e evolução independente.
3. **A ordenação do Story Pipeline não é verificável.** FR-20 exige os checks aplicáveis, mas não preserva explicitamente a sequência anterior ao desenvolvimento (`creation/adoption → adversarial review → security review quando aplicável → readiness`) nem a sequência posterior (`tests → code review → security review → final verification → closure`).

## Conflitos

- Não há conflito material entre a fonte e o PRD/addendum.
- A fonte apresenta uma lista fixa de transições; o PRD prefere que o Workflow as declare. Isso é compatível somente se houver um baseline de transições válidas que nenhuma declaração possa contradizer.
- A fonte pode ser lida como exigindo sempre toda a sequência de Story Pipeline, enquanto FR-20 usa “quando aplicáveis” para security review e checks. A proporcionalidade do PRD é coerente com o restante da SPEC, mas a regra de aplicabilidade precisa ser declarada, não implícita.

## Correções recomendadas

- Acrescentar a FR-3 um critério de aceite com a matriz baseline de transições normais, de interrupção e de reabertura; extensões devem ser versionadas e não podem contornar Readiness ou Exit Contract.
- Em FR-7, declarar `review_policy`, `security_policy` e `approval_policy` como dimensões distintas do Workflow, ainda que sua representação física fique para a arquitetura.
- Em FR-20, tornar o pipeline pré/pó-desenvolvimento um cenário de Contract Test e exigir que qualquer Gate omitido registre regra de não aplicabilidade.

