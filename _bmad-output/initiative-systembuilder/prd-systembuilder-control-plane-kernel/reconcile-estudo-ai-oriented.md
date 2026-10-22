# Reconciliação — Estudo AI-Oriented Software Development

Fonte: `docs/0004_011_AI_Oriented_Software_Development_Estudo_Final_2026-09-20.pdf`

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

Regra aplicada: o estudo sustenta tese e critérios de valor, mas não substitui decisões de produto do v0.6. Recomendações de uma V1 ampla de assurance são avaliadas contra o slice do Kernel, não automaticamente incorporadas.

## Cobertura forte

- O PRD adota a tese central: geração/término do agente não equivale a software aceitável; valor está em Evidence, accountability, estado e governança. Cobertura: problema, resultado pretendido, PD-4/PD-5, FR-10 a FR-13. Fonte: PDF pp. 4–5, 15–17, 29.
- O SystemBuilder não se torna “mais um agente” e integra executores existentes atrás de adapters/capabilities. Cobertura: visão, non-goals, FR-16/FR-17 e NFR-7. Fonte: PDF pp. 20–23.
- Readiness como evidência, não score opaco, está em NFR-10, Gate verificável, Assurance Record e counter-metrics. Fonte: PDF p. 17.
- Factos verificáveis, Evidence, limitações, risco residual e julgamento humano aparecem em FR-4 a FR-6, FR-10, FR-13 e nos Stage Profiles. Fonte: PDF pp. 17, 21, 25.
- Requisitos-to-code traceability, Evidence independente do agente e integridade longitudinal são suportados por FR-11/FR-12, Baseline/revisão e Capability Matrix. Fonte: PDF pp. 22–24.
- Permissões por risco, gates humanos e least privilege estão em FR-8, NFR-5/NFR-6 e na política de Override. Fonte: PDF pp. 21–25.
- O recorte rejeita foundation model, IDE, cloud completa, observabilidade substituta, low-code generalista, deploy irrestrito e certificação automática — alinhado aos non-goals da fonte. Fonte: PDF p. 23.
- Usuários primários cobrem maker/indie, founder/PM/designer e developer profissional; equipes/enterprise são explicitamente não alvo deste slice, consistente com v0.6. Fonte: PDF pp. 12–13.
- Métricas do PRD focam invariantes de governança e evitam maximizar PASS/Evidence/velocidade, coerentes com “outcome aceito por atenção humana e custo”, embora ainda incompletas economicamente. Fonte: PDF pp. 14–15, 28.

## Gaps materiais

1. **Classificação epistemológica de outputs não é obrigatória.** O estudo requer separar fato, inferência e ambiguidade; o PRD registra Provenance/Evidence, mas não exige `claim_status` ou equivalente nos outputs recuperados, especialmente em Ideation/Brainstorm e PRD. Fonte: PDF pp. 21–22, 25.
2. **Baseline reproduzível está subespecificada.** O Stage Profile Coding/Story exige build/checks aplicáveis, mas não requer capturar ambiente/configuração, comandos/checks, versões e Evidence suficiente para repetir build/test/lint/typecheck/security. Fonte: PDF pp. 21–22.
3. **Contabilidade de execução e custo está ausente.** A fonte exige tempo, tokens, sandboxes, tests, retries e custo por finding/mudança; NFR-11 mede latência/retries/intervenções, mas não usage/cost. Fonte: PDF pp. 14, 22, 25, 28.
4. **Categorias de permissão por risco não estão enumeradas.** FR-8 fala Capability e permissões escopadas, sem distinguir leitura, alteração de código, schema, produção, deploy, infra e secrets. Mesmo com produção fora do scope, o contrato deve ser extensível e impedir colapso em “permitido/não permitido”. Fonte: PDF pp. 22–23.
5. **Handoff/dossier portátil não possui representação mínima.** Está fora da implementação ampla, mas o estudo e o v0.6 tratam continuidade/handoff como valor. Baseline e audit queries fornecem componentes, porém não há export/projection contract mínimo. Fonte: PDF pp. 21–22.

## Conflitos e resolução

- **V1 repository-first com análise de migrations e dossier portátil versus Kernel de Stage Contract:** o estudo recomenda uma V1 de assurance mais ampla. O v0.6 posterior escolhe Stage Contract + Assurance Core como próximo artefato; prevalece o slice do Kernel. Repo, migration e handoff completo ficam como capabilities/perfis posteriores.
- **Wedge de profissionalização/migração:** o estudo o trata como hipótese comercial não comprovada. O PRD acerta ao não resolver buyer, pricing, GTM ou Discovery.
- **“Production Assurance & Graduation Layer” versus ciclo SystemBuilder ideia→deploy:** são enquadramentos compatíveis, mas o relatório v0.6 define a visão vigente. O PRD deve usar o estudo como tese, não como substituto do produto.
- **Métricas econômicas versus invariantes do Kernel:** custo por mudança aceita e review minutes exigem execução/pilotos; o PRD corretamente usa metas absolutas apenas para invariantes. Ainda assim, instrumentação de usage deve nascer no ledger para não impedir medição futura.
- **Git/PR como mecanismo obrigatório:** o estudo o recomenda; v0.6 deixa integrações repo abertas. No Kernel, deve permanecer necessário apenas para Contract Test real de Coding/Story, como OQ-10.

## Correções recomendadas

- Acrescentar classificação epistemológica versionada para claims materiais (`verified_fact`, `inference`, `assumption`, `ambiguity` ou vocabulário equivalente), com Evidence e origem.
- Definir uma Evidence manifest reproduzível para checks de Coding/Story: ambiente/configuração, comando/check, versão, resultado, timestamp e referência de output, sem escolher tecnologia.
- Instrumentar usage por Execution no Assurance Record/ledger: duração, retries, provider/model, tokens/compute quando disponíveis e referência de custo; billing permanece fora do escopo.
- Criar taxonomia extensível de permission/action risk e exigir policy decision por categoria; implementar no slice somente as categorias usadas.
- Definir Baseline/Audit Query como base de um `handoff projection` exportável mínimo, deixando dossier completo e portabilidade operacional para evolução posterior.
