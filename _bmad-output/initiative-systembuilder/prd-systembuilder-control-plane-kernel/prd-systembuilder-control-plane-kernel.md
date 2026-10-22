---
title: SystemBuilder SaaS — governança e execução isolada
status: final
created: 2026-10-08
updated: 2026-10-22
---

# PRD: SystemBuilder SaaS

## 0. Propósito, precedência e reconciliação

Este PRD orienta o fatiamento do **SystemBuilder SaaS**: um produto multi-tenant que governa o ciclo de desenvolvimento assistido por IA e orquestra BMAD e executores através de adapters. O Control Plane Kernel continua a ser o núcleo de governança, mas já não é o limite do produto. Console/BFF, Control Plane, Execution Plane, Runner Manager, Git por Project, storage de artefactos, adapters e delivery de cliente pertencem à superfície do SaaS.

Precedência: `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf` governa arquitetura, tenancy, runners, dados, integrações, infraestrutura e delivery; `docs/0005_01-Fluxo_Desenvolvimento_Painel_BMAD.pdf` governa o fluxo; `DESIGN.md` e `EXPERIENCE.md` são o contrato UX; a Architecture Spine aprovada concretiza os ADs. Este PRD preserva o Stage Contract, Readiness, Gate, Override/Exceção Formal, Assurance, Ledger, Baseline, impacto e Capability Matrix quando compatíveis. Cada requisito indica fontes de rastreabilidade; detalhes ainda não decididos estão em Open Questions, nunca implícitos.

## 1. Visão e tese

O SystemBuilder torna um processo BMAD e executor-driven compreensível, governado e auditável sem se tornar outro agente de geração. Para cada Tenant e Project, o produto transforma intenção e artefactos em um Workflow versionado: mostra a próxima ação elegível, aplica Gates, executa trabalho não confiável isoladamente e conserva a evidência necessária para explicar uma decisão.

A tese é que uma execução terminada não é uma entrega aceite. Uma entrega só avança quando o Control Plane confirma contexto, políticas, Evidence, Gate e Exit Contract; e continua reconstruível quando uma decisão ou artefacto upstream muda. O código do cliente persiste em Git, não num workspace; o Runner Manager executa tentativas descartáveis; e Laravel mantém a autoridade final sobre estado governado e autorização.

O produto suporta o fluxo completo Ideia → Descoberta → Product Brief/Brainstorm → PRD → UX → Arquitetura → Epics → Stories → Desenvolvimento → Validação do Epic → Retrospectiva → Entrega. O primeiro Perfil BMAD obrigatório para Coding/Story é mais estrito: `PRD → UX → Architecture → Project Context → Coding/Story`. Isso não substitui BMAD: o SystemBuilder governa a elegibilidade, adapters e evidência à sua volta. [ASSUMPTION: A-1 — a primeira adoção será acompanhada por operadores internos; o segmento comercial inicial ainda não foi escolhido.]

## 2. Utilizadores, trabalhos e jornadas

### 2.1 Utilizadores e Jobs To Be Done

- **Lia, operadora de Project:** quer avançar trabalho sem entender comandos internos de agentes; precisa de ver estado, razão, próxima ação e decisões que lhe competem.
- **Rui, revisor/auditor:** quer explicar por que uma progressão ou entrega foi aceite sem procurar logs brutos de um runner.
- **Marta, administradora de Tenant / maintainer de integração:** quer ligar e homologar uma combinação BMAD, provider, modelo ou executor sem expor credenciais nem alterar regras de domínio.
- **Nuno, responsável de entrega de cliente:** quer aprovar, acompanhar e reverter um deploy de Project sem misturá-lo com a operação do SaaS.

### 2.2 Jornadas principais

- **UJ-1 — Lia inicia e governa uma Stage.** No Console, Lia escolhe um Project autorizado; vê a Stage Revision, Readiness, Gate, Evidence segura e próxima ação. Autoriza uma Execution apenas se o Perfil, Capability e permissões a permitem; um resultado do runner só se torna candidato até Laravel o aceitar. Ela fecha uma Baseline quando o Exit Contract passa. *(UX EXPERIENCE UJ-1; 0005 §§3–4; Spine AD-3, AD-5, AD-7.)*
- **UJ-2 — Lia trata uma condição sem a ocultar.** Um `CONCERNS` ou `FAIL` mostra a regra, Evidence e risco residual. Override só é apresentado para uma classe declarada; `BLOCKED` exige remover a causa ou uma Exceção Formal pré-declarada, limitada e auditada. *(UX EXPERIENCE UJ-2/UJ-2b; Spine AD-5.)*
- **UJ-3 — Lia reabre trabalho e gere impacto.** Uma mudança preserva a Baseline e cria nova Stage Revision; dependências passam por análise explícita `unchanged`, `REQUIRES_REVIEW` ou `STALE` e não avançam com impacto incompleto. *(UX EXPERIENCE UJ-3; Spine AD-5.)*
- **UJ-4 — Rui reconstrói uma decisão.** A partir de Requirement, Artifact, Evidence, Gate ou entrega, Rui percorre o Ledger e Traceability Links do Project autorizado até Inputs, versão de Workflow, Execution, decisões e risco residual, sem logs brutos. *(UX EXPERIENCE UJ-4; 0005 §5; Spine AD-5, AD-6.)*
- **UJ-5 — Marta homologa uma integração.** Marta vê Capability Matrix, versões e Contract Test; uma combinação incompatível falha de forma explícita em vez de trocar provider, modelo ou executor. *(UX EXPERIENCE UJ-5; 0006 §6; Spine AD-8.)*
- **UJ-6 — Nuno entrega um Project de cliente.** Depois de Gates próprios de entrega, Nuno aprova um Deployment Run para um ambiente do cliente, recebe health check e pode accionar o rollback definido, sem obter acesso ao pipeline de deploy do SystemBuilder. *(0005 §4.12; 0006 §§9–10; Spine AD-10.)*

## 3. Limites de produto e princípios vinculativos

- O browser comunica apenas com o Console/BFF Next.js; Laravel reautoriza Tenant, Project e ação, é a autoridade final e a única fonte de mutação de estado governado. *(0006 §§4–5; Spine AD-2, AD-3.)*
- O contexto Tenant/Project é derivado no servidor de identidade e membership verificadas; identificadores, headers ou query strings fornecidos pelo browser não concedem escopo. *(0006 §§4–5; Spine AD-4.)*
- O Execution Plane é isolado do Control Plane. Runner Manager e runner não decidem Gate, autorização ou progressão. *(0006 §§7–8; Spine AD-3, AD-7.)*
- Dados governados/auditoria vivem em MySQL; Redis serve apenas coordenação/cache/fila; código persistente vive em Git por Project; artefactos, uploads, exports e backups vivem em storage S3 compatível; workspace é descartável. *(0006 §§4, 9–12; Spine AD-6.)*
- Provider Credential, Model Policy, Provider Adapter, Execution Adapter, BMAD Adapter e Deployment Provider Adapter são capacidades independentes. Não há fallback implícito. *(0006 §6; Spine AD-8, AD-10.)*
- Workflows, políticas, Capability Matrix, Guardrails e decisões materiais são versionados e append-oriented. Uma Exceção Formal não converte `BLOCKED` em `PASS`. *(0005 §§3–4; Spine AD-5, AD-11.)*

## 4. Glossário

- **Tenant** — organização isolada que possui Memberships, Projects e políticas próprias.
- **Project** — unidade isolada dentro de um Tenant, com Workflow, Git repository, artefactos e contexto governado próprios.
- **Console/BFF** — superfície Next.js consumida pelo browser; compõe a interface e chama Laravel server-to-server, sem possuir regras de domínio.
- **Control Plane** — módulos Laravel que governam identidade, autorização, estado, Workflow, Ledger, integração e aceitação de resultado.
- **Execution Plane** — Runner Manager e runners que executam trabalho não confiável fora do Control Plane.
- **Stage Contract / Stage Revision** — contrato versionado de uma etapa e a ocorrência versionada desse contrato em um Project.
- **Readiness / Gate / Exit Contract** — avaliação de entrada, guarda de progressão e critérios verificáveis de fecho de uma Stage Revision.
- **Override / Exceção Formal** — decisões distintas: Override aceita somente falha não essencial declarada; Exceção Formal é pré-declarada para um blocker e transição, com controlo compensatório.
- **Execution** — tentativa autorizada e correlacionada de executar Workflow; também pode expor um `run_id` para adapters.
- **Assurance Record / Evidence / Artifact** — registo governado mínimo da Execution, prova referenciada e output publicado com identidade/integridade durável.
- **Ledger / Baseline / Traceability Link** — histórico append-oriented, estado aceite preservado e relação navegável entre artefactos, decisões, execução, código, teste e entrega.
- **Capability Matrix** — capacidades, limites, autoridade e compatibilidade versionadas de BMAD/provider/model/executor/adapter.
- **Project Constitution** — contexto controlado e versionado de decisões duradouras, ADRs, guardrails, convenções e práticas proibidas, enviado de forma mínima à Execution.
- **Deployment Run** — execução governada de delivery de um Project de cliente através de Deployment Provider Adapter; não é deploy do SaaS.

## 5. Requisitos funcionais

### 5.1 Contexto, Console e autoridade

#### FR-1 — Contexto Tenant/Project derivado no servidor

O SystemBuilder deve derivar Tenant, Project e permissões de Membership autenticada em cada comando, consulta, objeto, job, callback, credencial Git e token de Execution. A topologia inicial usa MySQL shared-schema com tenant_id explícito nos registos scoped; futura base dedicada pode mudar a colocação, não a semântica de scope. Todas as entidades scoped devem falhar fechadas sem contexto. **Fontes:** 0006 §§4–5; Spine AD-4; UX EXPERIENCE Foundation.

**Consequências verificáveis:** manipular IDs de Project/Execution/Artifact/Evidence não lê, muta nem confirma a existência de recurso fora do scope; cada acesso negado relevante gera facto auditável sanitizado; URL pré-assinada, quando usada, limita um objeto e ação autorizados e expira.

#### FR-2 — Console/BFF sem autoridade de domínio

O browser deve usar somente endpoints do Console/BFF; o BFF mantém sessão segura e entrega modelos de visualização autorizados. Laravel reautoriza toda operação sensível e é o único componente que cria/aceita estado governado, Ledger, Gate, Baseline ou autorização de Execution. **Fontes:** 0006 §§4–5; Spine AD-2, AD-3; UX EXPERIENCE Foundation/Accessibility Floor.

**Consequências verificáveis:** o browser não acede diretamente a Laravel, Redis, Runner Manager ou runner; tokens internos não chegam ao JavaScript; BFF não pode aceitar uma transição se Laravel a rejeitar; streaming de progresso chega apenas via BFF e não concede controlo de runner.

#### FR-3 — Console operacional e auditável

O Console deve apresentar Project autorizado, Stage Revision, Readiness, razão, próxima ação elegível, Gate, Evidence segura, Baseline, impacto e rastreabilidade. Mantém as decisões UX fechadas: desktop/laptop executa ações críticas; mobile é consulta; estados têm texto e razão, não só cor; conteúdo restrito é sanitizado antes de renderizar. **Fontes:** UX DESIGN §§Brand–Components; UX EXPERIENCE §§Foundation, Information Architecture, State Patterns, Accessibility Floor; 0005 §8.

**Consequências verificáveis:** `BLOCKED` não apresenta Override genérico; cada ação está ligada a política/permite aplicável; tentativa cross-scope não revela recurso; fluxo crítico é operável por teclado e a superfície observa WCAG 2.2 AA conforme o contrato UX.

### 5.2 Workflow e Kernel governado

#### FR-4 — Stage Contract e lifecycle versionados

O Control Plane deve manter Stage Contract, Stage Profile, Workflow, políticas de review/security/approval, entradas, outputs, executor, permissões, decisões humanas, Gates, retries, transições, dependências e regras de reabertura como contratos declarativos e versionados. O lifecycle preserva `READINESS`, execução, fecho, interrupção e reabertura sem reescrever Baseline. **Fontes:** 0005 §§3–4; Spine AD-5; UX EXPERIENCE §§Readiness e lifecycle.

**Consequências verificáveis:** a conclusão do runner não conclui Stage; uma Stage somente avança por Gate e Exit Contract declarados; uma reabertura cria Stage Revision ligada por `supersedes`; versões usadas em decisões continuam recuperáveis.

#### FR-5 — Readiness, Gate, Override e Exceção Formal

O produto deve avaliar `PASS`, `CONCERNS`, `FAIL` ou `BLOCKED` com regra, Inputs/Evidence, razão e ação permitida. Override preserva condição original, política, aprovação, escopo, validade e risco residual; apenas uma falha não essencial declarada pode ser overrideable. Exceção Formal é distinta, pré-declarada por blocker/transição, com aprovação e controlo compensatório. **Fontes:** 0005 §§3–4, §7; Spine AD-5; UX EXPERIENCE §§Readiness, Override e Exceção Formal.

**Consequências verificáveis:** `BLOCKED` nunca se torna `PASS` por Override; Gate sem persistência de decisão não progride; Findings bloqueantes impedem a transição salvo a regra formal aplicável; a condição original permanece auditável depois da exceção.

#### FR-6 — Perfil BMAD verificável, sem salto de Gate

O SystemBuilder deve governar a rota completa do fluxo 0005 e, para o perfil obrigatório de desenvolvimento BMAD, impor a ordem versionada `PRD → UX → Architecture → Project Context → Coding/Story`. Recomendação de agente, artefacto existente, importação ou execução anterior não satisfaz pré-requisito por si só. **Fontes:** 0005 §§4, 9; Spine AD-5, AD-11; PRD anterior FR-23.

**Consequências verificáveis:** Coding/Story bloqueia quando PRD, UX, Architecture ou Project Context está ausente, stale ou não validado; cada recomendação/autorização/transição é avaliada contra a revisão ativa do Perfil; exceção só é aceite se o Perfil a pré-declara, aprova, audita e limita a uma transição.

#### FR-7 — Adapters e Capability Matrix independentes

O Control Plane deve manter separadamente BMAD Adapter, AI Provider Adapter, Credential Profile, Model Policy, Execution Adapter, Git Provider Adapter e Deployment Provider Adapter, com versões, capacidades observadas, limites e autoridade de artefacto. **Fontes:** 0006 §§6, 9–10; Spine AD-8, AD-10; UX EXPERIENCE UJ-5.

**Consequências verificáveis:** combinação não homologada ou capability ausente falha explicitamente; nenhuma regra de domínio depende de provider/modelo/executor específico; o sistema não faz substituição silenciosa; Credential Profile pode ser revogado sem trocar executor ou Workflow.

### 5.3 Execution Plane, Git e artefactos

#### FR-8 — Execution autorizada e Runner Manager

Laravel deve criar a Execution com Tenant, Project, Workflow, revisão, política, branch/base commit quando aplicável, provider/model/executor autorizados e limites. Runner Manager só consome job autorizado e entrega resultados candidatos correlacionados; Laravel valida política, correlação e sequência antes de aceitar qualquer efeito governado. **Fontes:** 0006 §§7–8; Spine AD-3, AD-7, AD-13.

**Consequências verificáveis:** retry recebe tentativa nova e limitada; callback duplicado, fora de sequência, expirado ou revogado é deduplicado/rejeitado/quarentenado; perda de runner não altera estado aceite; cancelamento, timeout e falha são factos correlacionados no Ledger.

#### FR-9 — Runner efémero e least-privilege

Cada Execution deve correr em workspace e runner efémeros, rootless e resource-limited, com checkout de um Project, BMAD versionado read-only, contexto aprovado e credenciais temporárias estritamente necessárias. **Fontes:** 0006 §§7–8, 11–12; Spine AD-7, AD-9.

**Consequências verificáveis:** runner não tem modo privilegiado, Docker socket, MySQL, Redis, APP_KEY, credenciais S3 globais, segredos globais/outro provider, path/token/workspace de outro Project ou acesso runner-to-runner; testes negativos cobrem escape/mount e repo errado; destruir o workspace não perde código publicado em Git nem artefacto aceite.

#### FR-10 — Git por Project e artefactos governados

Cada Project deve ligar-se a um Git repository isolado. Execution usa credencial temporária restrita a repository/ref; commit/diff/base commit ligam-se a Workflow, Story e Execution. Artifact publicado recebe metadata governada e referência ou hash durável em storage S3 compatível. **Fontes:** 0006 §§9, 12, 14; Spine AD-6, AD-10.

**Consequências verificáveis:** checkout/credencial de repository ou branch não autorizado falha; merge protegido requer Gates declarados; Git é a verdade de código e workspace não é; Artifact pode ser reidentificado após runner ser destruído sem tornar o seu conteúdo automaticamente seguro para o browser.

#### FR-11 — Separação de fontes de verdade

O produto deve usar MySQL para metadata, estado e auditoria governados; Redis exclusivamente para coordenação/cache/fila; storage S3 compatível para artefactos, uploads, exports e backups; Git para código persistente; e workspace de runner como material descartável. **Fontes:** 0006 §§4, 9–12; Spine AD-6.

**Consequências verificáveis:** Redis ou workspace não podem ser a única fonte de Ledger, Baseline, Artifact aceite ou código; backup/export possui referência governada; recuperação de artefacto/backups não exige host de runner.

### 5.4 Assurance, continuidade e contexto controlado

#### FR-12 — Assurance, Ledger e rastreabilidade append-oriented

Cada Execution relevante deve produzir Assurance Record com identidade/correlação, actor/origem, tempo, Workflow/Stage/Profile e versões, inputs autorizados, executor/provider/model quando aplicável, outputs, Evidence references, Findings, Gates, decisões, risco residual e base commit/guardrails quando aplicável. O Ledger preserva factos por append e Traceability Links navegam Requirement → UX → Story → código/commit → teste → release quando existentes. **Fontes:** 0005 §§5–7; 0006 §§6, 8–9; Spine AD-5, AD-13; UX EXPERIENCE UJ-4.

**Consequências verificáveis:** correção cria evento/revisão, não reescreve facto aceite; Evidence é referenciada, não duplicada indiscriminadamente; auditoria reconstrói uma progressão sem logs brutos; mutação e dispatch assíncrono usam factos e correlação consistentes.

#### FR-13 — Baseline, reabertura e impacto downstream

Uma Stage concluída cria Baseline preservada. Mudança material cria revisão nova, avalia cada relação downstream como `unchanged`, `REQUIRES_REVIEW` ou `STALE`, e impede autorização/fecho dependente enquanto a análise está incompleta ou falha. **Fontes:** Spine AD-5; UX EXPERIENCE UJ-3; PRD anterior FR-13–FR-15.

**Consequências verificáveis:** nenhum artefacto downstream é apagado ou modificado automaticamente; incerteza de materialidade resulta em `REQUIRES_REVIEW`, nunca `unchanged`; cada disposição mostra revisões, regra, Evidence, razão e próxima ação.

#### FR-14 — Project Constitution e memória controlada

O Control Plane deve guardar Project Constitution versionado de ADRs, segurança, guardrails, convenções, dependências autorizadas e práticas proibidas. Uma Execution recebe somente a revisão aprovada e o contexto mínimo aplicável; aprendizagens de retrospectiva só se tornam activas por revisão governada. **Fontes:** 0005 §6; Spine AD-12.

**Consequências verificáveis:** runner não recebe histórico ilimitado; mudança de Constitution é rastreável e pode tornar dependência stale; a revisão usada fica registada no Assurance Record quando influenciar uma Execution.

### 5.5 Perfis iniciais e adoção controlada

#### FR-15 — Stage Profiles iniciais

O produto deve fornecer Stage Profiles versionados para Ideia/Discovery/Product Brief, PRD e Coding/Story. O primeiro produz problema, público, resultado, hipótese e Product Brief; o segundo torna requisitos, métricas, escopo, regras, dependências e critérios de aceitação prontos para UX/Architecture; o terceiro exige Story Readiness antes de Development e testes, code review, security review quando declarada aplicável e verificação antes de concluir. **Fontes:** 0005 §§4.1–4.9; Spine AD-5, AD-11; UX EXPERIENCE UJ-1.

**Consequências verificáveis:** cada Profile declara os seus Inputs, Outputs, Evidence, Gates e regra de não-aplicabilidade; Coding/Story não chega a READY FOR DEV com dependência, requisito, UX/Architecture aplicável ou Finding bloqueante em aberto; a conclusão de código só ocorre após o Gate de saída declarado.

#### FR-16 — Importação e adoção controladas

O SystemBuilder deve permitir que um Artifact ou contexto existente entre num Workflow por IMPORT_ADOPTION explícito, com identidade, origem, autoridade, ownership, versão, consistência, gaps e Readiness registados. Importar não equivale a validar nem satisfaz Gate. **Fontes:** 0005 §§3–5; Spine AD-5, AD-11; PRD anterior FR-21.

**Consequências verificáveis:** um Artifact importado sem proveniência/completude suficiente fica em AWAITING_USER, CONCERNS, FAIL ou BLOCKED conforme política, nunca é tratado como pré-requisito validado; a origem permanece navegável no Ledger; a decisão de adoção é reversível por nova revisão, sem apagar o input.

### 5.6 Delivery de cliente, distinto do SaaS

#### FR-17 — Deployment Provider Adapter e Deployment Run

O SystemBuilder deve tratar delivery de um Project de cliente como capability por Deployment Provider Adapter, separada da pipeline de deploy do próprio SaaS. Environments, SecretSet, approvals, rollout, health check, release e rollback são contratos governados e auditáveis. **Fontes:** 0005 §§4.6, 4.12; 0006 §§9–10; Spine AD-10.

**Consequências verificáveis:** Development Runner não recebe credencial permanente de produção por defeito; Deployment Run só inicia após Gates declarados; rollback e outcome ficam ligados à release/requisitos; falha de delivery não altera automaticamente a Baseline de código aceite.

## 6. Requisitos não funcionais

- **NFR-1 — Isolamento multi-tenant e multi-Project.** Todas as fronteiras scoped falham fechadas; uma suíte de conformidade testa IDOR/leitura/mutação cross-tenant e cross-project em Project, Execution, Artifact, Evidence, Git, objeto e callback. **Fontes:** 0006 §§4–5, 22; Spine AD-4; UX EXPERIENCE UJ-4.
- **NFR-2 — Segredos e apresentação segura.** Segredos nunca aparecem no browser, prompt persistido, stdout/stderr, log, Ledger, Artifact ou Evidence; credenciais são cifradas fora da master key de base de dados, materializadas temporariamente, revogadas e redigidas antes de persistência ou apresentação. A conformidade testa cada superfície proibida, inclusive erro/callback. **Fontes:** 0006 §6; Spine AD-8; UX DESIGN Do's and Don'ts.
- **NFR-3 — Contenção de runner.** Runner rootless, efémero, least-privilege e resource-limited não pode alcançar DB, Redis, Docker socket, host, S3 global, outro Project ou secret não autorizado. A conformance suite prova negação de escape, mount, egress/credencial/repo errados e limpeza após término. **Fontes:** 0006 §§7, 11–12, 22; Spine AD-7, AD-9.
- **NFR-4 — Integridade de autoridade e fluxo.** Browser, BFF, Runner Manager, adapters e webhooks não contornam Laravel; artefacto/agente/recomendação não salta Gate; provider/model/executor incompatível falha explicitamente. **Fontes:** 0006 §§4–6; Spine AD-2, AD-3, AD-8, AD-11.
- **NFR-5 — Auditabilidade e durabilidade.** Factos materiais são append-oriented, correlacionados, versionados e persistidos antes de transição; deduplicação, ordenação e validação de assinatura/replay/scope impedem callback ou evento indevido de mutar estado. **Fontes:** 0006 §§8, 14; Spine AD-5, AD-13.
- **NFR-6 — Disponibilidade recuperável por fase.** MySQL/Redis permanecem privados; backups cifrados ficam fora da VM operacional e restore é testado antes e periodicamente depois da entrada em produção. Falha de runner não corrompe estado aceite. Não há SLO/RTO/RPO numérico até decisão explícita. **Fontes:** 0006 §§10–12; Spine AD-9.
- **NFR-7 — Acessibilidade e superfície.** Console mantém WCAG 2.2 AA, quatro locais suportados, texto/razão para estados e ações críticas apenas em desktop/laptop; mobile mantém consulta segura. **Fontes:** UX DESIGN; UX EXPERIENCE Foundation/Accessibility Floor; Spine conventions.
- **NFR-8 — Limites de delivery.** Deploy de cliente usa secret/approval/rollback próprios e nunca partilha por defeito a identidade, pipeline ou credenciais de produção do SaaS. **Fontes:** 0006 §§9–10; Spine AD-10.

## 7. Escopo e não-objetivos

### 7.1 MVP de arquitetura operacional no Hetzner

Em escopo: Console/BFF, Laravel Control Plane, tenancy e Projects; Stage Contract/Readiness/Gate/Assurance/Ledger/Baseline/impacto; Perfil BMAD; Runner Manager e runners separados/rootless; Git por Project; object storage; adapters de AI/provider/executor/Git/deploy; delivery de cliente governado; rede privada para dados, edge público e backup/restore verificáveis. A topologia de arranque é operacionalmente simples, mas código de cliente não coabita com app ou dados privados. **Fontes:** 0006 §§10–12, 15; Spine AD-1–AD-11.

### 7.2 Crescimento horizontal

O produto deve poder escalar app/BFF, workers, runners e storage de forma independente, introduzindo load balancing, Redis dedicado, pool/scheduler de runners, telemetria e controlo de egress quando o volume/risco o justificar. Isto é uma direção de capacidade, não compromisso atual com produto de orquestração específico. **Fontes:** 0006 §15; Spine AD-9.

### 7.3 Hardening enterprise

O produto reserva interfaces para sandbox reforçada, pools/regiões/keys/storage dedicados, egress estrito e tenancy dedicada quando threat model, contrato ou regulação o exigirem. **Fontes:** 0006 §§7, 15; Spine AD-4, AD-7, AD-9.

### 7.4 Não-objetivos e limites explícitos

- Não substituir BMAD, criar um novo agente generalista, ou duplicar cegamente semântica nativa do BMAD.
- Não permitir que workspace, Redis, logs do host ou sucesso do runner se tornem fonte de verdade.
- Não comprometer nesta revisão Kubernetes, microVMs, database-per-tenant, provider/model/executor inicial, CI/CD, IaC, IdP/IAM exato, região, retenção ou SLOs.
- Não implementar billing/planos, compliance/certificação completa, marketplace de integrations, IDE próprio ou suporte enterprise antes das decisões correspondentes.
- Não confundir delivery de cliente com deploy/operação do próprio SaaS.

## 8. Métricas e evidência de sucesso

- **SM-1 — Conformidade de isolamento:** a release candidata passa todos os cenários de negação cross-tenant/cross-project, runner escape, credencial/repository errado e acesso a serviços proibidos. Valida FR-1, FR-9, NFR-1, NFR-3.
- **SM-2 — Ausência de fuga de segredo na suíte:** a bateria de secret-seeding/redaction não encontra o valor em browser, prompts persistidos, stdout/stderr, logs, Ledger, Artifact ou Evidence. Valida FR-7, NFR-2.
- **SM-3 — Integridade de governança:** cenários de referência não permitem fechar Stage por sucesso de runner, pular o Perfil BMAD nem converter `BLOCKED` em `PASS`; cada decisão aceita é reconstruível. Valida FR-4–FR-6, FR-12–FR-13, NFR-4–NFR-5.
- **SM-4 — Integridade de delivery:** cada Deployment Run de referência evidencia aprovação, secret scope, release, health check e rota de rollback sem usar pipeline/credencial do SaaS. Valida FR-17, NFR-8.
- **SM-5 — Utilidade humana instrumentada:** o produto mede tempo até próxima ação elegível, intervenções, falhas, retries, custo/usage quando disponível e completude de Assurance, sem meta numérica antes de baseline. Valida UJ-1–UJ-6, FR-12. [ASSUMPTION: A-2 — instrumentação de usage/custo será possível nos adapters homologados.]

**Counter-metrics:** não maximizar `PASS`, volume de Evidence, menor intervenção humana ou velocidade de execução se reduzir Gate, explicabilidade, segurança, Baseline ou rollback.

## 9. Riscos e dependências

- **Escape/isolamento insuficiente de runner:** mitigado por NFR-3, hosts separados e conformance suite; evolução para sandbox reforçada segue OQ-6.
- **Fuga de secret por output ou Evidence:** mitigada por NFR-2, modelo de visualização seguro e testes de seed/redaction; classificação/retenção detalhada segue OQ-5.
- **Fluxo BMAD tornar-se um bypass de artefactos existentes:** mitigado por FR-6 e NFR-4.
- **Delivery confundir ambientes/credenciais:** mitigado por FR-17 e NFR-8; o primeiro adapter depende de OQ-8.
- **Complexidade de produto excessiva no Console:** mitigada por modo Operate, divulgação progressiva e o contrato UX existente; a taxonomia de modos é OQ-7.

## 10. Assumptions Index

- **A-1 (§1):** primeira adoção é acompanhada por operadores internos; beachhead comercial está por decidir.
- **A-2 (§8):** adapters homologados poderão expor usage/custo para instrumentação, sem significar que todos têm a mesma granularidade.

## 11. Open Questions

| ID | Questão, owner e gatilho |
| --- | --- |
| OQ-1 | **IdP, MFA e RBAC/ABAC.** Owner: Architecture + Product. Decidir antes de onboarding externo; Laravel continua a autorização final. *(0006; Spine Deferred.)* |
| OQ-2 | **Primeiro provider/model/executor, método de ligação e fixtures de Contract Test.** Owner: Product + Architecture. Decidir antes de homologar o primeiro Stage Profile executável; não presumir que assinatura de consumo concede API. *(0006 §6; Spine AD-8.)* |
| OQ-3 | **Runtime rootless, host image e protocolo Runner Manager (payload, retry, cancelamento, idempotência).** Owner: Architecture. Decidir antes do primeiro runner e provar AD-7. *(0006 §§7–8; Spine Deferred.)* |
| OQ-4 | **CI/CD, IaC, região Hetzner, sizing, firewall detalhado, monitorização e SLOs.** Owner: Architecture + Operations. Decidir antes de produção; não escolher Kubernetes por omissão. *(0006 §§10–15; Spine Deferred.)* |
| OQ-5 | **Residência/DR de object storage, retenção, deleção/export e classificação de dados.** Owner: Product + Architecture + Compliance. Decidir antes do primeiro Tenant de produção ou obrigação contratual. *(0006 §§10–12; Spine Deferred.)* |
| OQ-6 | **Gatilho para gVisor/Kata/microVM, egress proxy, pool dedicado e database-per-tenant.** Owner: Security + Architecture. Decidir quando threat model ou risco de cliente o exigir. *(0006 §7, §15; Spine AD-4, AD-7.)* |
| OQ-7 | **Taxonomia Interaction Mode.** Owner: Product + UX. Reconciliar `Guided`/`Advanced` da UX com `Guided`/`Collaborative`/`Expert` de 0005 antes de expor configuração geral; continua independente de Autonomy Policy. *(0005 §2; UX EXPERIENCE Foundation; Spine Deferred.)* |
| OQ-8 | **Primeiro Deployment Provider Adapter e política de ambientes/aprovações.** Owner: Product + Architecture. Decidir antes de disponibilizar delivery de cliente fora de cenário controlado. *(0005 §§4.6, 4.12; Spine AD-10.)* |
| OQ-9 | **Modelo comercial, billing e quotas.** Owner: Product. Decidir antes de funcionalidade comercial; não bloqueia os invariantes de governança. *(Spine Deferred.)* |

## 12. Critério de saída do PRD

O PRD está pronto para fatiamento quando um Epic consegue preservar os contratos de tenancy, BFF/Laravel, runner, fontes de verdade, governança de Stage, Perfil BMAD, Git/artefacto, adapter e delivery sem escolher uma OQ. Cada Story resultante deve referenciar os FR/NFR aplicáveis e incluir a evidência de segurança/isolamento aplicável.
