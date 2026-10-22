---
id: SPEC-systembuilder-control-plane-kernel
companions:
  - stage-contract.md
  - assurance-core.md
  - workflow-and-gates.md
  - architecture-reference.md
  - traceability-and-audit.md
  - open-questions.md
sources:
  - ../prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md
---

> **Contrato canónico.** Esta SPEC e os ficheiros em `companions:` constituem o contrato completo para construir, testar e validar o Kernel. O PRD em `sources:` está integralmente absorvido e serve apenas para auditoria.

# SystemBuilder Control Plane Kernel

## Why

O trabalho assistido por IA não demonstra, por si só, que uma etapa está pronta, autorizada, verificável ou segura para avançar. O Kernel é a primeira fatia operacional que torna a progressão por Stage explícita e auditável — sem substituir o BMAD nem virar um gerador de código — para que operador e revisor expliquem decisões, evidência, risco residual e impacto downstream sem reconstruir logs brutos.

## Capabilities

- **CAP-1**
  - **intent:** O sistema pode criar e inspecionar um Stage Contract genérico e versionado em contexto de Tenant e Project.
  - **success:** Cada contrato válido contém Identity, Entry, Readiness, Execution, Output, Closure e Dependencies; cada Stage Revision preserva a versão usada e entidades fora do Tenant/Project autenticado não são expostas.

- **CAP-2**
  - **intent:** O sistema pode avaliar Readiness de uma Stage e determinar a progressão permitida.
  - **success:** Para os checks aplicáveis produz exatamente `PASS`, `CONCERNS`, `FAIL` ou `BLOCKED`, com gaps, warnings, blockers, Evidence e próxima ação elegível.

- **CAP-3**
  - **intent:** O operador pode tratar uma condição de Readiness permitida sem ocultar seu resultado original.
  - **success:** Cada Override ou Exceção Formal registra condição, transição, política, aprovação, vigência, risco e Evidence; somente `FAIL` nominalmente tolerado pode ter Override, e `BLOCKED` permanece `BLOCKED` sem bypass genérico.

- **CAP-4**
  - **intent:** O sistema pode registrar o Assurance Core mínimo para cada Execution relevante desde Ideation/Brainstorm.
  - **success:** Cada Assurance Record correlaciona contexto, actor/origin, engine/version, inputs, outputs, Gate, resultado e Evidence; campos obrigatórios ausentes impedem que a Execution sustente um Gate de fechamento.

- **CAP-5**
  - **intent:** O sistema pode preservar uma Baseline concluída e reabrir trabalho como uma nova Stage Revision.
  - **success:** Reabrir uma revisão `COMPLETED` conserva sua Baseline, cria uma revisão ligada por `supersedes`, reavalia Readiness e não presume válidos Gates ou aprovações afetados.

- **CAP-6**
  - **intent:** O sistema pode analisar impacto downstream de uma mudança upstream sem atualização destrutiva.
  - **success:** Cada relação conhecida é classificada explicavelmente como `STALE`, `REQUIRES_REVIEW` ou `unchanged` pela ordem determinística definida, com delta, regra, Evidence e próxima ação; informação insuficiente resulta em `REQUIRES_REVIEW`.

- **CAP-7**
  - **intent:** O Control Plane pode declarar o Workflow de uma Stage sem acoplar a regra de negócio a UI, provider ou internals do BMAD.
  - **success:** O Workflow versionado declara inputs, output, executor, políticas, guardrails, Gates, retries, timeouts e transições; troca por executor homologado compatível não muda sua semântica.

- **CAP-8**
  - **intent:** O sistema pode manter ledger e rastreabilidade entre o trabalho governado e suas decisões.
  - **success:** Um revisor parte de Requirement, Decision, Baseline, Artifact, Evidence, Gate ou Finding e reconstrói autorizações, Execution, versões, evidência, risco e impacto sem logs brutos do runner.

- **CAP-9**
  - **intent:** O sistema pode integrar BMAD por um Adapter versionado e uma Capability Matrix.
  - **success:** As operações `inspect`, `validate_entry`, `invoke`, `collect`, `reconcile` e `project_state` são homologadas por Contract Test; Capability ausente ou incompatível falha explicitamente, sem fallback silencioso.

- **CAP-10**
  - **intent:** O sistema pode aplicar o mesmo Stage Contract a Ideation/Brainstorm, PRD e Coding/Story, preservando as suas diferenças.
  - **success:** Contract Test cobre lifecycle, Readiness, Evidence e Exit Contract dos três Stage Profile, incluindo os Gates pré e pós-desenvolvimento de Coding/Story.

- **CAP-11**
  - **intent:** O sistema pode aplicar um Perfil de fluxo BMAD versionado em vez de inferir o próximo passo a partir de um agente.
  - **success:** No perfil adotado, Coding/Story não recebe `PASS` sem PRD aprovado, UX validada, Architecture aprovada e Project Context validado e rastreável; requisito ausente ou stale limita as ações às transições elegíveis.

- **CAP-12**
  - **intent:** O Control Plane pode autorizar e reconciliar cada Execution com escopo limitado.
  - **success:** Execution expirada, incompatível ou não autorizada é rejeitada; resultados aceitos correlacionam com a Stage Revision, divergência material BMAD/SystemBuilder interrompe progressão automática e término técnico não conclui a Stage.

- **CAP-13**
  - **intent:** O sistema pode adotar Input ou Artifact preexistente antes de usá-lo numa Stage Revision.
  - **success:** A adoção registra identidade, origem, ownership, Provenance, versão e consistência; material sem fonte ou ownership suficiente não satisfaz Readiness silenciosamente.

- **CAP-14**
  - **intent:** O sistema pode aplicar Interaction Mode e Autonomy Policy como dimensões independentes.
  - **success:** O default é `Guided` + `Supervised`; alterar uma dimensão não altera a outra e uma ação continua limitada por Capability, permissão, Gate e risco.

## Constraints

- O Control Plane é autoridade para identidade, estado, política, Gate, aprovação, Provenance, Evidence, risco residual, impacto e homologação; BMAD continua autoridade sobre artefactos e estado nativos quando adequado.
- O Execution Plane executa agentes, código, testes e reviews isoladamente; não recebe dados globais ou de outro Project, e não executa no processo do Control Plane.
- Eventos de governação são append-oriented e Baselines/artefactos aceites sobrevivem a reabertura, supersession ou perda de runner; falha de persistência bloqueia a transição governada.
- Isolamento Tenant/Project, integridade de Evidence, autenticação/autorização, Capability/permissão, segredo e Finding de segurança bloqueante não podem ser contornados por Override ou Exceção Formal.
- Segredos nunca aparecem em browser, ledger, stdout/stderr persistido, prompt archive, Artifact ou Evidence; a entrega ao Execution Plane é temporária, escopada e revogável.
- Regras de produto não dependem de provider, modelo, executor, path, enum, prompt ou wiring interno do BMAD; seleção incompatível falha explicitamente.
- O MVP assume um operador primário, uma macro Stage primária ativa e serialização em escopos conflitantes; o default é `Guided` + `Supervised`.
- `COMPLETED` exige Exit Contract e Gate de fechamento aprovados; sucesso técnico de agente não basta.

## Non-goals

- Construir a plataforma SystemBuilder completa ou uma rota ideia→deploy.
- Substituir BMAD ou criar um agente próprio de geração de código.
- Definir storage físico, infraestrutura de produção, CI/CD, deploy, operação pós-deploy ou colaboração multiutilizador.
- Decidir Product Discovery, beachhead, pricing, jurídico, compliance completo ou GTM.
- Fazer inventário BMAD skill-by-skill, seleção final de provider/modelo, billing ou otimização de custos.

## Success signal

O primeiro Epic pode demonstrar nos três Stage Profile que o Kernel governa Stage Contract, Readiness, execução autorizada, Assurance, Gate, fechamento, reabertura e impacto. Os cenários demonstram zero bypass genérico de `BLOCKED`, zero salto não autorizado no perfil BMAD e uma resposta auditável para cada decisão sem logs brutos.

## Assumptions

- Thresholds operacionais e metas de eficiência serão definidos depois de uma baseline instrumentada; os invariantes de governação são as métricas absolutas iniciais.
- O consumo inicial será serviço/API de domínio ou ferramenta interna; a superfície final de UX é definida separadamente.
- As políticas operacionais iniciais bastam para validar o Kernel mantendo evolução futura para colaboração e concorrência.
- O Adapter pode anteceder inventário BMAD completo somente com falha explícita de Capability não homologada.
- `REQUIRES_REVIEW` é disposição de validade de relação; `STALE` também é estado de Stage Revision quando impacto material invalida Evidence vigente.

## Open Questions

- Quais IDs, cardinalidades e ownership físicos ligam as entidades governadas?
- Que garantias de persistência, versionamento, retenção, exportação, redaction e recuperação sustentam Ledger e Baseline?
- Qual é o contrato mínimo Control/Execution Plane para mensagens, idempotência, revogação, falhas e entrega de Evidence?
- Como secrets, código, prompts, logs e Evidence são escopados, cifrados, redigidos, retidos e destruídos?
- Qual política de compatibilidade vale para Stage Contract, Stage Profile, Workflow, Adapter e Capability Matrix?
- Qual versão/commit BMAD e inventário de operações entram na primeira homologação?
- Qual provider/executor inicial, integração Coding/Story, profundidade por perfil, forma de `IMPORT_ADOPTION`, superfície, métricas/SLOs e beachhead serão escolhidos nas interfaces explícitas?
