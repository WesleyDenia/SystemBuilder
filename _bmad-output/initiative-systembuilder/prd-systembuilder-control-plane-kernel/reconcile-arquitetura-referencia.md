# Reconciliação — Arquitetura de Referência Painel BMAD

Fonte: `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf`

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

Regra aplicada: esta referência é anterior ao estado v0.6. Mecanismos físicos permanecem candidatos; decisões de produto do v0.6 prevalecem.

## Cobertura forte

- A separação Control Plane/Execution Plane, a execução tratada como não confiável e a sobrevivência do Control Plane à falha de runner estão em PD-8, FR-8/FR-9 e NFR-5/NFR-8. Fonte: PDF pp. 3–4, 7–8, 15.
- O isolamento por Tenant/Project e a relação `Tenant → Project → Stage/Workflow → Execution → Artifact/Evidence` estão em FR-1, Glossário e NFR-4. Fonte: PDF pp. 5, 7, 15.
- Workflow declarativo com input/output contract, Artifact, guardrails, review/security/approval policy, retry e timeout está coberto por FR-7. Fonte: PDF p. 10.
- Abstração de provider/executor, Capability Matrix e ausência de fallback silencioso estão em PD-9, FR-16/FR-17 e NFR-7. Fonte: PDF pp. 6–7.
- Secrets temporários, escopados, revogáveis e ausentes de logs/browser/Artifact/Evidence estão em FR-8 e NFR-6. Fonte: PDF p. 7.
- Git/base commit/diff/commit no perfil Coding/Story aparece em FR-20; a persistência de eventos e Artifact já aceitos além da vida do runner aparece em FR-9, FR-11 e NFR-3. Fonte: PDF pp. 8, 10.
- A cadeia de rastreabilidade e o registro de provider/model, BMAD, guardrails, base commit, alterações manuais e aprovações estão substancialmente cobertos por FR-10 a FR-12 e NFR-9. Fonte: PDF p. 11.
- Os critérios adversariais de cross-tenant, segredo e rastreabilidade foram traduzidos em FR-1 e NFR-4/NFR-6, com métricas de integridade em SM-1/SM-5. Fonte: PDF pp. 14–15.
- O addendum preserva corretamente runner efêmero/rootless, workspace único, limites de recurso, Git, object storage, fila, Runner Manager e separação credential/provider/adapter como alternativas, sem elevá-las a arquitetura aprovada.

## Gaps materiais

1. **Usage/cost accounting não integra o Assurance Core.** A referência inclui cost/usage ledger e consumo por provider/model; FR-10 não exige usage metadata e os sinais de §10 não incluem tokens/compute/custo. Fonte: PDF pp. 5–6, 11.
2. **Os mínimos verificáveis da fronteira de execução estão genéricos.** NFR-5 proíbe acesso global/outro Project, mas não registra como critérios candidatos: workspace único por Execution, nenhum acesso direto a DB/cache/storage master, nenhum host socket, credenciais temporárias e limites/timeout. O addendum preserva os mecanismos, porém a futura arquitetura pode não perceber quais propriedades são obrigatórias versus exemplos. Fonte: PDF pp. 7–9, 13–15.
3. **Versões de guardrails e base commit não são campos explicitamente obrigatórios.** FR-10 fala engine/configuração/política e referências de Inputs; FR-20 exige diff/commit, mas o vínculo da Execution à versão de guardrails e ao base commit deveria ser inequívoco quando aplicável. Fonte: PDF pp. 8, 11.
4. **Cancellation/idempotency/late results estão apenas em OQ-5.** O lifecycle da fonte pressupõe job, streaming, timeout, destruição e retorno de resultado; FR-9 cobre reconciliação e falha, mas não define resultado tardio após revogação, duplicidade de evento ou retry da mesma autorização. Fonte: PDF p. 8.
5. **Falhas de boundary deveriam gerar evento de segurança auditável.** NFR-4 exige auditoria para cross-tenant, mas NFR-5/NFR-6 não exigem explicitamente ledger para tentativas de acesso proibido, secret injection indevida ou capability fora do escopo. Fonte: PDF pp. 7, 9, 13–15.

## Conflitos e resolução

- **Git por Project como fonte de verdade:** a arquitetura anterior o trata como decisão; v0.6 mantém integração com repositórios aberta. Para este PRD, Git deve permanecer dependência do cenário Coding/Story e candidato arquitetural, não decisão global do Kernel.
- **Stack/topologia física (Next.js, Laravel, MySQL, Redis, Hetzner, shared schema):** não pertence ao PRD e não é decisão vigente de produto. O addendum resolve corretamente como alternativa histórica.
- **Providers iniciais nomeados:** OpenAI/Codex, Anthropic/Claude e um provider de baixo custo são sugestões antigas; v0.6 deixa shortlist e forma de conexão abertas. O PRD acerta ao manter abstração e OQ-9.
- **Security review sempre como gate versus profundidade proporcional:** a referência recomenda tratá-la como gate. O v0.6 posterior determina Assurance Core graduado por etapa/risco; FR-20 usa “quando aplicável”. Prevalece o v0.6, desde que a aplicabilidade seja declarativa e auditável.
- **Fluxo integral ideia→entrega:** é contexto da plataforma, não requisito de implementação deste slice.

## Correções recomendadas

- Acrescentar usage/cost references opcionais-por-aplicabilidade ao Assurance Core e um sinal instrumentado de consumo por Execution; não definir billing.
- Na arquitetura posterior, converter as propriedades da fronteira listadas no addendum em acceptance constraints verificáveis, mantendo tecnologia aberta.
- Explicitar `base_revision/base_commit` e versão de guardrails/policy no Assurance Record quando a Execution operar sobre código ou política versionada.
- Resolver OQ-5 com idempotency key, ordenação/correlação de eventos, revogação, timeout, retry e política para resultados tardios/duplicados.
- Exigir evento auditável para violações de escopo/capability/secret boundary, com redaction e sem persistir o segredo.

