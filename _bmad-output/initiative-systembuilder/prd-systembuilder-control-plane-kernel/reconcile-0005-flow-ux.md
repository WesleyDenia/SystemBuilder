# Reconciliação — 0005 Fluxo BMAD + contrato UX

**Fontes inspecionadas.** `docs/0005_01-Fluxo_Desenvolvimento_Painel_BMAD.pdf` (set. 2026), `ux-systembuilder-control-plane-kernel/DESIGN.md` e `EXPERIENCE.md` (ambos finalizados em 2026-10-15). Este extract informa a atualização SaaS; não altera o PRD.

## Decisões de produto a transportar

| Requisito verificável para o PRD SaaS | Fonte / ponteiro | Reconciliação necessária |
|---|---|---|
| O produto governa e orquestra BMAD e executores por adapters; não os substitui nem expõe comandos/agentes como fluxo obrigatório ao utilizador. | 0005 §§1, 8 e 10 | Manter como princípio e non-goal. A consola traduz intenção, estado, decisão, risco e próxima ação elegível. |
| Todo Workflow Profile versionado declara Stages, objetivo, inputs obrigatórios, artefatos/outputs, actor ou adapter responsável, revisões, dependências/decisões, Exit Contract e Gate. | 0005 §3 | Tornar obrigatório no perfil `PRD → UX → Architecture → Project Context → Coding/Story`; uma recomendação, agente ou artefato pré-existente não satisfaz Gate por inferência. |
| A transição entre Stages exige Gate com Evidence e registo de decisão; informação em falta leva a pendência/readiness, não a salto. | 0005 §§3–4.4, 10; UX `Interaction Primitives`, `State Patterns` | Preservar Stage Contract/Readiness/Gate/Assurance/Ledger. A aprovação do PRD exige requisitos essenciais, contradições relevantes resolvidas e escopo apto a orientar UX e arquitetura (0005 §4.4). |
| A UX vem antes da arquitetura no perfil BMAD; os seus fluxos, estados, acessibilidade e referências passam a input governado da arquitetura. | 0005 §4.5 | É regra do perfil, não uma afirmação de que todo Workflow Profile futuro tenha a mesma ordem. |
| A arquitetura analisa os outputs precedentes e regista recomendação, alternativas, consequências e ADRs; infraestrutura, delivery, segredo, observabilidade, backup e rollback são decididos a nível suficiente antes de Coding/Story. | 0005 §§4.6–4.6.4 | No SaaS, não escolher tecnologia não decidida: exigir decisão/ADR ou OQ. Distinguir deploy do próprio SystemBuilder de delivery do Project do cliente. |
| Coding/Story só fica apto depois de Story, revisão adversarial, correções, security review, correções e Story Readiness (AC testáveis, dependências, arquitetura/ADRs, UX, segurança, testes, sem TODO bloqueante). | 0005 §4.8 | Modelar como Gate/assurance do perfil ou perfil derivado; não codificar os nomes de comandos BMAD como única implementação. |
| Uma Story não fica concluída só por código gerado: build, testes, code review, security review, correções e verificação final passam antes de `DONE`. | 0005 §4.9 e §10 | Exigir resultados/evidence correlacionados e política para achados bloqueantes. |
| Antes de release, validar dimensões técnica, produto e UX; a entrega mantém release, versão, requisitos/Stories incluídos, checks pós-deploy e rollback quando aplicável. | 0005 §§4.10–4.12 | Delivery de cliente é capability de adapter, com approvals/secrets/rollback próprios; não confundir com deploy/operar o SaaS. |
| Cada Requirement relevante tem ID estável; a cadeia é Discovery → Requirement → UX Flow → Epic → Story → commit/código → teste → release. | 0005 §5 | Generalizar para links tipados/versionados no Ledger e Artifact/Evidence, sem tornar workspace de runner fonte de verdade. |
| Agentes recebem pacote versionado de contexto mínimo: decisões/ADRs, baseline de segurança, guardrails, dependências autorizadas/proibidas e convenções. Retrospectivas podem criar guardrails persistentes para execuções futuras. | 0005 §6 e §4.11 | Ligar Project Context ao perfil e a cada Execution; não injetar histórico indiscriminado. |
| Segurança é Gate pré e pós implementação; Finding apresenta severidade, risco, recomendação, estado e link ao artefato, e bloqueia segundo política até correção ou tratamento formal permitido. | 0005 §7 | Aplicar a semântica atual: `BLOCKED` não tem bypass; apenas Exceção Formal pré-declarada, limitada e auditável. |

## Contrato UX fechado a preservar

- **Operação governada e explicável:** Readiness primeiro; resultado único, regra/política, Evidence, risco residual, actor/origem, tempo e próxima ação. Não usar logs brutos ou score opaco como explicação. (`DESIGN.md` §§Brand & Style, Colors; `EXPERIENCE.md` §§Foundation, Voice and Tone.)
- **Guardas de lifecycle:** `PASS`, `CONCERNS`, `FAIL`, `BLOCKED`, `AWAITING_USER`, `ACTIVE`, `READY_TO_CLOSE`, `COMPLETED`, `REOPENED` e `STALE` mantêm tratamento explícito; fechamento requer Exit Contract + Gate; Baseline é imutável e reabertura cria revisão `supersedes`. Override é restrito; `BLOCKED` só admite resolução ou Exceção Formal. (`EXPERIENCE.md` §§Component Patterns, State Patterns, UJ-1..UJ-3.)
- **Rastreabilidade/auditoria:** Ledger append-oriented e Traceability recuperam Requirement, Decision, Baseline, Artifact, Evidence, Gate e Finding; correções são novo evento/versão. Pesquisa, filtros, cursor, breadcrumbs e comparação existem; MVP não tem grafo nem exportação. (`EXPERIENCE.md` §§Information Architecture, Component Patterns, UJ-4.)
- **Capacidades/adapters:** Capability/homologação de combinação BMAD/provider/executor requer Contract Test e Evidence; capability ausente ou combinação não homologada impede a ação e nunca causa fallback/troca silenciosa de provider, modelo ou executor. (`EXPERIENCE.md` IA, State Patterns, UJ-5.)
- **Segurança na apresentação:** Evidence só é preview quando explicitamente seguro/redigido; não mostrar secrets, stdout/stderr persistido ou logs brutos. A redação aplica-se a conteúdo, URL, `href`, título, tooltip, toast, acessibilidade e query string; acesso cross-tenant/project nega sem revelar existência. (`DESIGN.md` Do's/Don'ts; `EXPERIENCE.md` Accessibility Floor.)
- **Plataforma e acessibilidade:** desktop/laptop executa ações críticas; mobile consulta estado, razão, Evidence autorizada, auditoria e próxima ação sem aprovar/autorizar/fechar/excepcionar. shadcn/ui + `docs/theme.css`, tema claro/escuro, WCAG 2.2 AA, teclado, foco, redução de movimento, pt-PT/pt-BR/en/es e formatação local permanecem decisões fechadas. (`DESIGN.md` §§Layout, Components; `EXPERIENCE.md` §§Responsive, Interaction Primitives, Accessibility Floor.)

## Ampliações SaaS compatíveis (não reabrir o contrato)

1. **Contexto de acesso:** ampliar “console interno, uma operadora por Project” para membros/roles de Tenant e Project. A UI pode pedir/mostrar Tenant e Project ativos, mas autorização e contexto efetivo são derivados no servidor/BFF; recusas continuam sanitizadas e não enumeram recursos. Preserve a entrada “Contexto de Project” e não exponha dados de outro escopo.
2. **Console e planos:** manter Console como superfície de governança. Para o utilizador normal, revelar adapter, executor, runner ou plano apenas como capability, estado, versão, limitação e Evidence necessários à decisão; não como comandos internos. O browser fala somente com BFF é requisito de segurança/arquitetura, não uma nova superfície de UX.
3. **Integrações de Project:** acrescentar vistas governadas de ligação Git por Project, Artifact/object storage e adapters de AI/provider/executor/deploy. Segredos aparecem somente como referência/estado seguro (por exemplo, “configurado/indisponível/rotação requerida”), nunca como valor. Todas as ações usam o mesmo contexto, permissões, Gate, Evidence e Ledger.
4. **Delivery:** acrescentar uma distinção visível e auditável entre operação/deploy do SystemBuilder SaaS e entrega/deploy de um Project cliente. Apenas a segunda é uma capability de adapter e exige sua própria aprovação, secret scope, verificação pós-deploy e rollback.
5. **Execução isolada:** a UI nunca oferece workspace de runner como fonte de auditoria ou estado. Resultados entram por Artifact/Evidence governados e correlacionados; resultado sem correlação vai a rejeição/quarentena, como já determina UJ-1.

## Tensões resolvidas e itens que não devem regressar

- Não restabelecer a sequência completa de 0005 como workflow global rígido. O profile BMAD acima é a sequência exigida; outros profiles continuam declarativos/versionados.
- Não adoptar a regra histórica “Epic = uma Sprint”, tecnologias/stacks, fornecedor cloud, CI/CD ou modo de execução específicos do PDF. Tornam-se decisões de Architecture/ADRs ou OQs, conforme a arquitetura canónica.
- Não preservar a limitação “console interno/trabalho individual” como escopo do SaaS. Preserve a postura operacional, mas expanda tenancy/membership/Project sem enfraquecer os estados e guardas UX.
- Não permitir que “aprovação da operadora no MVP” implique autoaprovação universal: autoridade é sempre política/role/contexto derivado no servidor.

## Pointers de rastreabilidade para os FR/NFR atualizados

| Área a alterar/criar no PRD | Fontes diretas |
|---|---|
| Positioning BMAD, modos/autonomia e experiência progressiva | 0005 §§1–2, 8, 10; EXPERIENCE `Foundation`, `Voice and Tone` |
| Workflow Profile, Stage Contract, Gates e ordem BMAD | 0005 §§3–4.6, 10; EXPERIENCE `Interaction Primitives`, `State Patterns` |
| Coding/Story, Assurance, security, release/delivery | 0005 §§4.8–4.12, 7; EXPERIENCE `Component Patterns`, UJ-1..UJ-2b |
| Traceability, Project Context, learning/guardrails | 0005 §§5–6; EXPERIENCE `Information Architecture`, UJ-3..UJ-4 |
| BFF/scoping, secret safety, capability and no fallback | EXPERIENCE `Information Architecture`, `Accessibility Floor`, UJ-5; DESIGN `Do's and Don'ts` |
| Responsividade, i18n, WCAG e visual system | 0005 §8; DESIGN inteiro; EXPERIENCE `Responsive & Platform`, `Interaction Primitives`, `Accessibility Floor` |

## Memlog candidates for the parent run

- **change:** 0005 is reconciled as a source for governed workflow and product experience; its legacy full lifecycle is not reintroduced as a global fixed workflow.
- **decision:** The versioned BMAD Workflow Profile requires `PRD → UX → Architecture → Project Context → Coding/Story` and evidence-backed Gates; no agent, recommendation or pre-existing artifact may bypass one.
- **change:** UX decisions on readiness, formal exceptions, baseline/reopen, impact, governed audit, capability/no-fallback, evidence redaction, desktop-critical/mobile-view, WCAG and localisation are preserved and extended to SaaS membership/context.
- **decision:** Console UI distinguishes SaaS operation/deployment from client Project delivery; the latter remains adapter capability subject to its own approvals, secret scope and rollback.
