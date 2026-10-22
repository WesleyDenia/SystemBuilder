# Reconciliação — Relatório de Progresso SystemBuilder v0.6

Fonte: `docs/0021_Relatorio_Progresso_SystemBuilder_v0.6.pdf`

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

Regra aplicada: esta é a fonte de produto prioritária entre os quatro PDFs. Formulações históricas ou recomendações ainda não confirmadas no próprio relatório não foram promovidas a decisões apenas por aparecerem nele.

## Cobertura forte

- A fronteira **native-first** está fielmente preservada: BMAD conserva conteúdo/estado nativos adequados; o Control Plane mantém identidade de execução, gates, proveniência, Evidence, risco residual, reconciliação e staleness. Cobertura: visão; PD-8/PD-9; FR-9, FR-16 e FR-17. Fonte: PDF pp. 5–6, 10–11.
- O Stage Contract proposto no relatório — Identity, Entry, Readiness, Execution, Output, Closure e Dependencies — corresponde diretamente a FR-2 e ao Glossário. Fonte: PDF pp. 14–16.
- O State Model local está coberto integralmente em FR-3, inclusive `NOT_STARTED`, `READINESS`, `ACTIVE`, `AWAITING_USER`, `BLOCKED`, `READY_TO_CLOSE`, `COMPLETED`, `STALE` e `REOPENED`. Fonte: PDF pp. 7–8.
- `COMPLETED` condicionado ao Exit Contract, em vez de término técnico do agente/workflow, está coberto por PD-5, FR-8, FR-13 e pelos três Stage Profiles. Fonte: PDF pp. 8, 10, 19.
- Mudança upstream não destrutiva e disposições `REQUIRES_REVIEW`/`STALE` estão cobertas por PD-7, FR-14 e FR-15. Fonte: PDF pp. 8, 10, 19.
- BMAD Adapter versionado, operações `inspect → validate_entry → invoke → collect → reconcile → project_state`, homologação e Contract Tests aparecem em FR-16/FR-17 e NFR-12. Fonte: PDF pp. 6, 10–11.
- Assurance Core obrigatório desde Ideation/Brainstorm, com profundidade graduada, está coberto por PD-4, FR-10 e FR-18 a FR-20. Os campos recomendados no relatório estão todos representados; o PRD acrescenta Tenant, Stage Revision, configuração/política e correlações. Fonte: PDF pp. 6–7, 15–17.
- As quatro recomendações imediatas — readiness, override, Assurance Core e revisão preservando baseline — foram incorporadas coerentemente em PD-2 a PD-6, FR-4/FR-6/FR-10/FR-14. Fonte: PDF pp. 15–16.
- As simplificações confirmadas do MVP — um operador principal, uma Stage macro primária ativa, execução serial em escopos conflitantes e default Guided + Supervised — estão em usuários, PD-10 e A-4. Fonte: PDF pp. 1, 4–5, 10.
- O PRD mantém fora do recorte Discovery, GTM, pricing, jurídico, compliance completo, arquitetura física e plataforma inteira, coerente com os limites epistemológicos declarados no relatório. Fonte: PDF pp. 1–2, 18.

## Gaps materiais

1. **IMPORT_ADOPTION está fraco demais.** O relatório confirma entrada intermediária por fluxo explícito e a registra em D-025/RF-031; o PRD a reduz a A-7 e declara a jornada completa fora do escopo. Falta comportamento mínimo verificável: identificar Artifact existente, registrar autoridade/ownership/proveniência, validar completude/consistência, produzir gaps e somente então avaliar Readiness. Fonte: PDF pp. 8, 10, 19.
2. **Modo de interação e política de autonomia não têm requisito próprio.** PD-10 fixa apenas o default; FR-8 trata autorização escopada, mas não exige que `Guided/Advanced` e `Conservative/Supervised/Autonomous` sejam dimensões independentes, persistidas e não inferidas de persona. Isso é uma decisão explícita D-018/E-023. Fonte: PDF pp. 4–5, 10, 20.
3. **Identidade `run_id`/Execution precisa de mapeamento explícito.** FR-10 registra Execution, mas o relatório define `run_id` como identidade transversal mínima. Sem declarar equivalência ou cardinalidade, a arquitetura pode criar dois identificadores concorrentes ou omitir o identificador esperado pelo adapter. Fonte: PDF pp. 5, 15, 19.
4. **Escopo chamado “MVP” pode ser confundido com a primeira versão utilizável do produto.** O relatório reserva “primeira versão utilizável” para ideia→deploy; o PRD usa “MVP” para o Kernel de três perfis. O recorte é legítimo, mas o vocabulário pode sugerir que substitui a amplitude aprovada em D-002. Fonte: PDF pp. 1, 3, 10.
5. **Ações apresentadas ao usuário não estão vinculadas de modo verificável a Stage + modo + autonomia.** O requisito RF-006 do relatório não aparece como aceite observável. FR-7/FR-8 cobrem execução e autorização, mas não a elegibilidade/exposição de ações. Fonte: PDF pp. 3, 9, 19.

## Conflitos e resolução

- **Produto ponta a ponta versus primeiro recorte:** o relatório mantém ideia→deploy como amplitude da primeira versão utilizável; o PRD deliberadamente define apenas o primeiro recorte implementável. Não é conflito se o documento se nomear inequivocamente como *Kernel MVP/slice*, sem revogar D-002.
- **Quatro decisões “pendentes” no relatório versus decisões no PRD:** a seção 14 do relatório as apresenta como recomendações a confirmar. A SPEC canônica posterior já as formaliza e o PRD as adota. Registrar essa proveniência evita atribuir confirmação ao PDF v0.6 isoladamente.
- **IMPORT_ADOPTION fora do escopo versus D-025:** há conflito parcial. A jornada completa pode continuar fora, mas o mínimo funcional confirmado pelo v0.6 não deveria ser apenas assumption.
- **Guided-only operacional versus modelo Guided/Advanced:** o PRD pode implementar apenas Guided no primeiro slice, mas não deve modelar modo e autonomia como uma única dimensão nem tornar `Advanced` estruturalmente impossível.

## Correções recomendadas

- Promover A-7 para um FR de IMPORT_ADOPTION mínimo com critérios de aceite; manter a experiência completa fora do escopo.
- Acrescentar requisito/invariante que modele e registre separadamente Interaction Mode e Autonomy Policy, preserve o default `Guided + Supervised` e proíba inferência rígida por persona.
- Declarar no Glossário/FR-10 se `Execution.id` é o `run_id` transversal ou se existe relação 1:N; exigir correlação inequívoca no ledger.
- Renomear consistentemente o recorte para “Kernel MVP” ou “primeiro slice implementável”, distinguindo-o do product MVP ideia→deploy.
- Acrescentar aceite para que ações elegíveis sejam calculadas por Stage, Workflow, modo, autonomia, permissões e Gate, sem transformar UI final em escopo.

