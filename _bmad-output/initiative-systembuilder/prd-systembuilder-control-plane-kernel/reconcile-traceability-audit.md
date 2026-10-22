# Reconciliação: Traceability And Audit

Fonte reconciliada: `spec-systembuilder-control-plane-kernel/traceability-and-audit.md`  
Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`

## Cobertura forte

- O propósito do ledger transversal está no problema, resultado pretendido, UJ-4, glossário e FR-10–FR-12.
- FR-11 e NFR-1 preservam audit append-oriented, correção por novo evento/versão, correlação causal e falha fechada: sem persistência não há transição governada.
- Tenant/Project scope, actor/origin, timestamp, target e correlação estão exigidos; NFR-4 cobre negação e auditoria de acesso cross-Tenant/Project.
- NFR-6 proíbe secrets no browser, ledger, stdout/stderr, prompt archive, Artifact e Evidence; FR-9/NFR-8 preservam eventos aceitos apesar de falha ou destruição do runner.
- Todos os nove Traceability Link iniciais estão no glossário e são exigidos por FR-12 com extensão versionada.
- As audit queries centrais estão semanticamente cobertas por UJ-4 e FR-12: ator, autorização, Inputs/versões, executor, Evidence, risco residual e navegação entre as entidades governadas sem logs brutos.

## Gaps

1. **A taxonomia mínima de eventos foi comprimida.** FR-11 enumera famílias de alto nível, mas não exige eventos importantes da fonte, como runner assigned/progress/cancelled; Override requested/rejected/revoked; Artifact imported/adopted/superseded/invalidated; Evidence missing/invalidated/accepted; lifecycle completo de Finding; e eventos de Adapter/Contract Test.
2. **As sete audit queries da fonte não formam uma suíte de aceite explícita.** FR-12 é amplo, mas não assegura nominalmente consultas sobre disposição downstream nem sobre divergência BMAD/SB e sua reconciliação.
3. **Logs do runner não têm requisito explícito de correlação por `execution_id`.** O PRD exige correlação dos resultados e dispensa logs brutos para auditoria, mas não declara que, se logs forem recebidos/persistidos, devem estar ligados à Execution e que storage do host nunca é fonte de verdade.
4. **Cobertura de acesso indevido pode ficar estreita.** NFR-4 fala genericamente em recursos cross-Tenant/cross-Project, o que é forte; um teste de conformidade nominal para Project, Execution, Artifact e Evidence, como na fonte, reduziria interpretação incompleta.

## Conflitos

- Não há conflito material; o PRD fortalece a fonte ao exigir 100% dos eventos definidos em FR-11 e falha fechada de persistência.
- A fonte menciona commits e Release na rastreabilidade. O PRD inclui commit/diff somente no Stage Profile Coding/Story e exclui entrega/deploy; o addendum documenta essa limitação. O modelo pode reconhecer referências externas sem implementar Release no MVP.
- “Append-oriented” é requisito lógico no PRD; garantias físicas de imutabilidade e retenção permanecem corretamente em OQ-3, portanto não devem ser presumidas como resolvidas.

## Correções recomendadas

- Anexar a FR-11 uma taxonomia mínima versionada de eventos cobrindo as nove famílias da fonte; permitir extensão sem tornar cada progress event obrigatório para todo executor.
- Transformar as sete audit queries da fonte em cenários de Contract Test de FR-12, incluindo impacto downstream e reconciliação BMAD/SB.
- Acrescentar a FR-9/NFR-8 que logs aceitos devem portar `execution_id` e que armazenamento do runner não é sistema de registro.
- Incluir testes negativos nominais de acesso cross-Tenant para Project, Execution, Artifact e Evidence, mantendo o requisito genérico para as demais entidades.

