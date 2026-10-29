---
id: SPEC-systembuilder-control-plane-kernel
companions:
  - stage-contract.md
  - assurance-core.md
  - workflow-and-gates.md
  - architecture-reference.md
  - traceability-and-audit.md
  - open-questions.md
  - requirements-and-ticket-impact.md
  - ../prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md
  - ../architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md
  - ../ux-systembuilder-control-plane-kernel/DESIGN.md
  - ../ux-systembuilder-control-plane-kernel/EXPERIENCE.md
  - ../../../AGENTS.md
sources:
  - ../../../AGENTS.md
  - ../prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md
  - ../architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md
  - ../ux-systembuilder-control-plane-kernel/DESIGN.md
  - ../ux-systembuilder-control-plane-kernel/EXPERIENCE.md
---

> **Contrato canónico.** Esta SPEC é o índice de rastreabilidade do contrato a construir, testar e validar; não substitui as fontes normativas em `sources:`. Os companions detalham o contrato mas não podem alargar, enfraquecer ou contrariar uma fonte normativa.

# SystemBuilder SaaS — Control Plane Kernel

## Autoridade das fontes e regra de revisão

As fontes em `sources:` são vinculativas. A Architecture Spine define os limites estruturais e os ADs; o PRD define requisitos de produto e respetiva reconciliação de fontes; `DESIGN.md` e `EXPERIENCE.md` definem o contrato de experiência; `AGENTS.md` fixa as regras de implementação e contenção no repositório. A Architecture Spine aprovada prevalece sobre o PRD Kernel anterior em conflitos de escopo arquitetural, tenancy, runners, dados, integrações, infraestrutura ou delivery; a sua reconciliação não elimina as regras de produto do PRD que sejam compatíveis. Esta SPEC não cria uma precedência local que possa contrariar essa regra.

Qualquer contradição normativa remanescente exige uma revisão explícita e aprovada da fonte competente, seguida de uma revisão versionada desta SPEC. Nem texto de companion, nem uma decisão de implementação, nem um ticket pode resolver silenciosamente uma contradição não abrangida pela precedência da Architecture Spine.

## Revisão de escopo aprovada

CAP-1–CAP-14 preservam os IDs e o contrato de Kernel aprovado. CAP-15–CAP-21 são escopo SaaS aprovado **aditivo**: tenancy e Console/BFF, execução isolada, Git/Artifacts, Project Constitution, Deployment Run e integridade assíncrona. Esta ampliação não escolhe provider, runtime, adapter de deployment, IAM, retenção ou infraestrutura de produção; essas escolhas permanecem abertas no registo de Open Questions.

## Why

O SystemBuilder torna workflows de desenvolvimento assistido por IA compreensíveis, governados e auditáveis sem se tornar outro agente de geração. Para cada Tenant e Project, o SaaS só permite que trabalho não confiável avance quando contexto, política, Evidence, Gate e Exit Contract o autorizam, e conserva uma explicação reconstruível quando artefactos ou decisões mudam.

## Capabilities

- **CAP-1**
  - **intent:** O sistema pode criar e inspecionar um Stage Contract genérico e versionado no contexto autorizado de Tenant e Project.
  - **success:** Cada contrato válido contém Identity, Entry, Readiness, Execution, Output, Closure e Dependencies; cada Stage Revision preserva a versão usada e entidades fora do scope não são expostas.

- **CAP-2**
  - **intent:** O sistema pode avaliar Readiness de uma Stage e determinar a progressão permitida.
  - **success:** Para os checks aplicáveis produz exatamente `PASS`, `CONCERNS`, `FAIL` ou `BLOCKED`, com gaps, warnings, blockers, Evidence e próxima ação elegível.

- **CAP-3**
  - **intent:** O operador pode tratar uma condição de Readiness permitida sem ocultar seu resultado original.
  - **success:** Cada Override ou Exceção Formal regista condição, transição, política, aprovação, vigência, risco e Evidence; só `FAIL` declarado tolerável pode ter Override e `BLOCKED` não recebe bypass genérico.

- **CAP-4**
  - **intent:** O sistema pode registar o Assurance Core mínimo para cada Execution relevante desde Ideia/Discovery/Product Brief.
  - **success:** Cada Assurance Record correlaciona contexto, actor/origin, engine/version, inputs, outputs, Gate, resultado e Evidence; campos obrigatórios ausentes impedem que a Execution sustente fecho.

- **CAP-5**
  - **intent:** O sistema pode preservar uma Baseline concluída e reabrir trabalho como nova Stage Revision.
  - **success:** Reabrir `COMPLETED` conserva a Baseline, cria revisão ligada por `supersedes`, reavalia Readiness e não presume válidos Gates ou aprovações afetados.

- **CAP-6**
  - **intent:** O sistema pode analisar impacto downstream de uma mudança upstream sem atualização destrutiva.
  - **success:** Cada relação é classificada explicavelmente como `STALE`, `REQUIRES_REVIEW` ou `unchanged` pela ordem definida, com delta, regra, Evidence e próxima ação; informação insuficiente resulta em `REQUIRES_REVIEW`.

- **CAP-7**
  - **intent:** O Control Plane pode declarar o Workflow de uma Stage sem acoplar regra de negócio a UI, provider ou internals do BMAD.
  - **success:** O Workflow versionado declara inputs, output, executor, políticas, guardrails, Gates, retries, timeouts e transições; troca por executor homologado compatível não muda sua semântica.

- **CAP-8**
  - **intent:** O sistema pode manter Ledger e rastreabilidade entre o trabalho governado e as suas decisões.
  - **success:** Um revisor parte de Requirement, Decision, Baseline, Artifact, Evidence, Gate ou Finding e reconstrói autorizações, Execution, versões, evidência, risco e impacto sem logs brutos do runner.

- **CAP-9**
  - **intent:** O sistema pode integrar BMAD, provider, modelo e executor por adapters versionados e Capability Matrix.
  - **success:** Combinações e operações homologadas têm Contract Test e autoridade de Artifact declarada; Capability ausente ou incompatível falha explicitamente, sem fallback silencioso.

- **CAP-10**
  - **intent:** O sistema pode aplicar o mesmo Stage Contract aos Stage Profiles iniciais de Ideia/Discovery/Product Brief, PRD e Coding/Story, preservando as suas diferenças.
  - **success:** Contract Test cobre lifecycle, Readiness, Evidence e Exit Contract dos três perfis, incluindo os Gates pré e pós-desenvolvimento de Coding/Story.

- **CAP-11**
  - **intent:** O sistema pode aplicar um Perfil BMAD versionado em vez de inferir o próximo passo a partir de um agente.
  - **success:** No perfil adotado, Coding/Story não recebe `PASS` sem PRD, UX, Architecture e Project Context presentes, validados, rastreáveis e não stale; requisito ausente limita as ações às transições elegíveis.

- **CAP-12**
  - **intent:** O Control Plane pode autorizar e reconciliar cada Execution com escopo limitado.
  - **success:** Execution expirada, incompatível ou não autorizada é rejeitada; resultados aceites correlacionam com a Stage Revision, divergência material interrompe progressão automática e término técnico não conclui a Stage.

- **CAP-13**
  - **intent:** O sistema pode adotar Input ou Artifact preexistente antes de o usar numa Stage Revision.
  - **success:** A adoção regista identidade, origem, ownership, Provenance, versão e consistência; material insuficiente não satisfaz Readiness silenciosamente.

- **CAP-14**
  - **intent:** O sistema pode aplicar Interaction Mode e Autonomy Policy como dimensões independentes.
  - **success:** O default é `Guided` + `Supervised`; alterar uma dimensão não altera a outra e cada ação continua limitada por Capability, permissão, Gate e risco.

- **CAP-15**
  - **intent:** O sistema pode derivar Tenant, Project e ação de membership autenticada em todas as fronteiras.
  - **success:** IDs, headers e query strings manipulados, URLs de objeto, credenciais Git, jobs e callbacks não leem nem mutam fora do scope; cada negação relevante é sanitizada e auditável.

- **CAP-16**
  - **intent:** Utilizadores autorizados podem operar e auditar trabalho governado através do Console/BFF sem acesso direto ao Control Plane ou runner.
  - **success:** Laravel é o autorizador final; ações críticas são só desktop/laptop, mobile é consulta segura, e o Console cumpre WCAG 2.2 AA, teclado, quatro locales, tema e Evidence sanitizada.

- **CAP-17**
  - **intent:** O sistema pode executar tentativa autorizada num runner efémero isolado.
  - **success:** Cada tentativa rootless e resource-limited recebe apenas checkout, contexto aprovado, BMAD read-only e credenciais temporárias escopadas; serviços proibidos, segredos globais e acesso cross-runner são negados.

- **CAP-18**
  - **intent:** O sistema pode governar Git e Artifacts duráveis de Project independentemente de runners descartáveis.
  - **success:** Credenciais de repository/ref e updates compare-and-swap são escopados à Execution; Artifact aceite tem metadata governada e referência ou hash durável e sobrevive à perda do runner.

- **CAP-19**
  - **intent:** O sistema pode manter Project Constitution versionado como contexto durável mínimo da Execution.
  - **success:** Cada Execution regista a revisão aprovada selecionada, não recebe histórico ilimitado e mudança material da Constitution pode disparar revalidação governada.

- **CAP-20**
  - **intent:** O sistema pode governar delivery de Project de cliente como Deployment Run independente.
  - **success:** Gates declarados, secrets por ambiente, aprovações, release, health check e rollback são correlacionados sem partilhar pipeline do SaaS nem alterar automaticamente a Baseline de código aceite.

- **CAP-21**
  - **intent:** O sistema pode despachar e aceitar trabalho assíncrono através de envelopes duráveis e canónicos.
  - **success:** Cada mutação material grava Ledger e outbox atomicamente; consumidores verificam origem/replay quando aplicável, deduplicam `event_id` e rejeitam sequência ou transição inválida.

## Constraints

Aplicam-se por referência, sem criar uma cópia local concorrente, a Policy de `AGENTS.md`, PRD §§3–6 (em especial FR-1–FR-17 e NFR-1–NFR-8), Architecture Spine AD-1–AD-13 e o contrato UX aprovado. Os controlos concretos de runner, artefacto, assíncrono, Evidence e delivery que esta SPEC precisa de tornar localmente rastreáveis estão citados na secção seguinte; qualquer conflito segue a regra de precedência e revisão explícita acima.

## Controlos aprovados por referência

- **Runner e resultados candidatos:** PRD FR-8–FR-9 e Architecture AD-7 exigem job autorizado, runner efémero/rootless/least-privilege e quarentena até Laravel validar correlação e política; o Runner Manager nunca aceita estado governado.
- **Artefactos e fontes de verdade:** PRD FR-10–FR-11 e Architecture AD-6/AD-10 exigem Git por Project, Artifact com metadata e referência/hash durável, e proíbem Redis ou workspace de runner de se tornarem verdade durável.
- **Mutações e assíncrono:** PRD FR-12 e Architecture AD-13 exigem Ledger e outbox atómicos, envelopes com correlação e sequência imutáveis, deduplicação e rejeição de transições inválidas.
- **Evidence e apresentação:** PRD FR-1–FR-3, NFR-2 e Architecture AD-8 exigem Evidence autorizada e sanitizada; logs brutos de runner, Evidence restrita e segredos não são um modelo de UI.
- **Delivery:** PRD FR-17 e Architecture AD-10 separam Deployment Runs de Project do deployment do SaaS e requerem adapter, ambientes, secrets, aprovações, health check e rollback governados.

## Non-goals

- Substituir BMAD, criar agente generalista próprio ou permitir que internals de agente definam governança.
- Transformar Redis, logs de host, workspace de runner ou sucesso técnico em fonte de verdade ou aceite de entrega.
- Escolher nesta SPEC IdP/IAM, provider/model/executor, runtime de runner, CI/CD, IaC, Kubernetes, região, retenção, SLOs, billing ou stack de observabilidade ainda abertos.
- Implementar marketplace de integrações, IDE próprio, planos comerciais, certificação/compliance completa ou suporte enterprise antes das decisões correspondentes.
- Confundir delivery de Project de cliente com deploy ou operação do SystemBuilder SaaS.

## Success signal

Uma release candidata demonstra isolamento de Tenant/Project e runner, ausência de fuga de segredo, impossibilidade de fechar por sucesso de runner, de saltar o Perfil BMAD ou de converter `BLOCKED` em `PASS`, e uma explicação auditável de cada decisão. A demonstração de um Deployment Run de referência com aprovação, secret scope, health check e rollback sem usar a pipeline ou credencial do SaaS é critério futuro: OQ-8 (a homologação histórica de BMAD) continua aberto e OQ-25 seleciona/homologa o Deployment Provider Adapter e a política de ambientes/aprovações. A reutilização anterior de OQ-8 para deployment foi migrada para OQ-25, sem reescrever a identidade histórica.

## Assumptions

- A primeira adoção é acompanhada por operadores internos enquanto o beachhead comercial permanece por decidir.
- Usage e custo são instrumentados onde adapters homologados os exponham, sem pressupor granularidade uniforme.
- Metas de eficiência e SLOs quantitativos serão definidos depois de uma baseline instrumentada; os invariantes de governação são as métricas absolutas iniciais.

## Open Questions

Ver `open-questions.md`. OQ-1–OQ-17 preservam a identidade histórica do registo do Kernel; OQ-18–OQ-26 são incertezas SaaS adicionadas nesta revisão, incluindo a seleção de Deployment Provider Adapter em OQ-25. Invariantes já aprovados são listados separadamente e não são Open Questions.
