# Delta de reconciliação — PRD Kernel → SystemBuilder SaaS

## Finalidade e regra de leitura

Este é um extrato de reconciliação para a atualização do PRD principal, não um novo PRD. A autoridade é `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf` (reflectida também na spine aprovada, AD-1 a AD-13); `0005` é autoridade de fluxo; UX é contrato de superfície quando compatível. O PRD atual e o addendum são evidência histórica, não limite de escopo. Cada alteração listada abaixo deve gerar uma linha append-only no `.memlog.md` pelo fluxo `memlog.py` durante a edição principal.

## Limitações e contradições materiais no estado atual

| Área | Evidência no PRD/addendum atual | Conflito / delta obrigatório |
| --- | --- | --- |
| Autoridade documental | §0 declara a SPEC e o relatório v0.6 canónicos e chama os PDFs de “contexto”; o memlog repete que v0.6 prevalece. | Substituir a precedência: `0006` é vinculativa para arquitetura, tenancy, execução, dados, integrações, infraestrutura e delivery; `0005` vincula o fluxo; UX vincula superfície. O relatório v0.6 pode ser contexto, nunca sobrepor-se a `0006`. |
| Enquadramento | Título, §0, §1.1 e §4 definem somente “Control Plane Kernel MVP”. | Reposicionar como **SystemBuilder SaaS** multi-tenant. O Kernel continua o núcleo governado, mas Console/BFF, Control Plane, Execution Plane, Runner Manager, Git, storage, adapters e delivery tornam-se capacidades do produto, não apenas mecanismos candidatos. |
| Escopo explicitamente excluído | §4.2 exclui plataforma completa, arquitetura física, repo/IDE/CI/CD/deploy/operação, providers e billing; §12/OQ-17 diz produção fora de escopo. | Revogar essas exclusões onde contradizem a SaaS spine. Distinguir “capacidade requerida” de “tecnologia/fornecedor ainda a decidir”: Git por projeto, runners, object storage, infraestrutura Hetzner e delivery de cliente são escopo; CI/CD, região, ferramenta de IAM, provider/modelo inicial, SLO, retenção e billing não são escolhas feitas. |
| Arquitetura e autoridade | FR-1/NFR-4 falam de Tenant/Project, mas não exigem contexto derivado no servidor, BFF exclusivo nem Laravel como autoridade final. | Inserir requisitos verificáveis para AD-1–4: browser só chama BFF (exceção URL pré-assinada limitada); BFF não decide domínio; Laravel reautoriza toda delegação e é o único mutador de estado governado; Tenant/Project vêm de membership autenticada, nunca de ID fornecido pelo browser, e falham fechados em DB, jobs, Git, storage e runner. |
| Dados e fontes de verdade | Addendum trata MySQL/Redis/Git/S3/workspace como alternativas históricas; NFR-3/NFR-8 não os separa por classe. | Converter AD-6 em requisitos: MySQL = metadados/estado/auditoria governados; Redis = cache/fila/coordenação; Git = verdade do código por projeto; S3 compatível = artifacts, uploads, exports e backups; workspace = descartável, nunca fonte de verdade. Exigir referência/hash governado para material aceite. |
| Execution Plane | FR-8 e NFR-5 são genéricos; addendum chama runner rootless/efémero apenas “candidato forte”. | Tornar AD-7 vinculativo: Runner Manager separado, job autorizado por Laravel, um runner/workspace rootless, efémero e limitado por recursos por tentativa; menor privilégio, quotas Tenant/Project, retry novo. Proibir ao runner MySQL, Redis, S3 global, Docker socket do host, application key, segredo global e path/token de outro Project. Saída expirada/revogada/duplicada é quarentenada; perda do runner não muda estado aceite. |
| Secrets e evidência | NFR-6 já é forte, mas falta a formulação integral de AD-8 e teste explícito da superfície BFF. | Exigir credenciais cifradas fora da master secret de DB e materializadas somente como secrets curtos e escopados no runner; redaction/sanitização em prompts, stdout/stderr, logs, Ledger, Artifact, Evidence, browser/UI e URLs. BFF apresenta modelo de Evidence autorizado/sanitizado, não raw evidence. |
| Adapters e substituição | FR-7/17 cobrem parte de Capability Matrix, mas não separam com clareza provider, credential/model policy, executor, Git e deploy. | Preservar BMAD Adapter e acrescentar/explicitar adapters independentes de AI/provider, credential/model policy, executor, Git provider e deployment provider. Qualquer capability incompatível/não homologada falha explicitamente: nunca fallback ou troca silenciosa de provider/model/executor. O SystemBuilder governa/orquestra BMAD; não o substitui. |
| Git e delivery do cliente | FR-20 considera Git mínimo OQ-10; addendum e §4.2 deixam deploy fora. | Fazer Git por Project e credenciais/branch por execução requisitos de produto: checkout/repo isolado, base commit, CAS do ref esperado e merge protegido por Gate. Delivery de cliente é capability via adapter, com ambientes, approvals, secrets, rollout, health check e rollback próprios. Separar isto do pipeline de deploy do próprio SaaS. |
| Fluxo BMAD | PD-11/FR-23 já fixam `PRD → UX → Architecture → Project Context → Coding/Story`, mas o §4 oferece apenas três Stage Profiles e não inclui a rota completa de `0005`/AD-5. | Manter essa sequência como pré-condição mínima da entrada Coding/Story e ampliar o **Perfil de Workflow versionado** para declarar a rota aplicável, artefactos, validação, revisions, Gates, dependências e exceções. Para o perfil inicial, acomodar `Idea → Discovery → Product Brief/Brainstorm → PRD → UX → Architecture → Epics → Stories → Development → Epic Validation → Retrospective → Delivery`, sem pressupor que todas as automações entram na primeira entrega. Nenhum agente, artefacto existente ou recomendação salta Gate. |
| UX e utilizadores | §2 exclui colaboração/equipa/enterprise; PD-10/FR-22 fecham `Guided` + `Supervised` e `Guided`/`Advanced`, enquanto `0005` especifica `Guided`/`Collaborative`/`Expert`; UX atual é de console interno com um operador. | Não prometer RBAC/ABAC enterprise nem colaboração antes de decisão. Mas definir SaaS com Tenant, memberships e Project autorizados. Preservar decisões UX fechadas de auditabilidade, evidência redigida e operadora MVP; abrir OQ explícita para reconciliar taxonomia de modo de interação (`Guided/Advanced` UX versus `Guided/Collaborative/Expert` 0005), independente de autonomia. |
| Infraestrutura e operação | §4.2, addendum e OQ-17 adiam produção; NFR-11 mede mas não fixa topologia. | Introduzir fases: (1) MVP operacional no Hetzner com borda pública Console/Laravel, dados privados e pool de runners separado/egress controlado; (2) crescimento horizontal independente de app, workers e runners; (3) hardening enterprise. Não escolher Kubernetes, microVMs/gVisor/Kata, database-per-tenant, região, CI/CD, IAM, retenção, SLO ou provider inicial: são OQs com owner e trigger. |
| Resiliência assíncrona | FR-9/11 tratam reconciliação, mas não definem dispatch durável, envelope, dedupe ou webhooks. | Adicionar requisito AD-13: mutação + outbox atómicos; envelopes canónicos versionados com scope/correlação/sequência; consumidores idempotentes; callback/webhook com assinatura, replay window, target scope e idempotência antes de virar candidato. |
| Project Constitution | Ausente do PRD. | Incluir capacidade de contexto controlado e versionado: Project Constitution de fatos aprovados duráveis; execução recebe a revisão mínima relevante; aprendizagem retrospectiva só ativa depois de revisão governada (AD-12). |
| OQs e memlog | OQs antigos têm classificação, mas owners/triggers incompletos; memlog preserva decisões de “fora de escopo” agora inválidas. | Manter as decisões históricas como superseded, nunca apagá-las. Cada remoção/alteração/novo OQ ganha entrada de change/decision/assumption com razão, owner e gatilho de revisão. |

## Estrutura estável sugerida para o PRD atualizado

1. Propósito, posicionamento SaaS e precedência de fontes.
2. Visão/problema, princípios e limites: governa BMAD/executores por adapters, não é substituto de BMAD.
3. Pessoas, tenancy/membership e jornadas SaaS (operar Project; executar/rever; manter integração; entregar cliente; operar SaaS), preservando a jornada de auditoria existente.
4. Escopo, não-escopo e fases de entrega (MVP Hetzner, escala horizontal, hardening), separando compromissos de OQs.
5. Regras de produto e invariantes arquitecturais (AD-1–13 como requisitos observáveis, não escolhas internas adicionais).
6. Workflow Profiles, Stage Contracts, Readiness/Gate/Override/Exceção, Assurance, Baseline/reabertura/impacto e Ledger/traceability.
7. Requisitos funcionais por capacidade (abaixo), com critérios de aceite e âncoras de fonte em cada requisito.
8. NFRs, incluindo segurança e isolamento testáveis.
9. Dados, integrações e delivery boundaries (fontes de verdade, adapters e separação SaaS/customer delivery).
10. Métricas/counter-metrics, riscos/dependências, assumptions e OQs.
11. Critério de prontidão para fatiamento e mapa de rastreabilidade de fontes.

## Agrupamento recomendado de requisitos

Preservar IDs FR-1–23 quando a semântica continuar compatível; alterar os seus critérios e anexar uma âncora de fonte. Acrescentar IDs novos em vez de reusar IDs para capacidades materialmente diferentes.

| Grupo | Requisitos a preservar/ampliar ou adicionar | Fontes mínimas |
| --- | --- | --- |
| Governação e workflow | FR-2–7, 10–15, 18–23; perfil completo versionado, guards de recomendação/autorização/transição/fecho, Project Constitution. | PRD atual; `0005` §§3–4; AD-5, AD-11, AD-12; UX EXPERIENCE. |
| Tenancy, identidade e Console | Ampliar FR-1/NFR-4; adicionar contexto servidor, memberships, BFF-only, Laravel final authority, resultados sanitizados e progress via BFF. | AD-1–4, AD-9; UX EXPERIENCE §§IA, segurança. |
| Execution Plane | Ampliar FR-8–9; adicionar Runner Manager, criação de runner rootless/efémero/limitado, quotas, token/checkout/contexto escopados, quarantine e revogação. | AD-7, AD-9. |
| Dados e auditabilidade | Ampliar FR-9–15; acrescentar classificação MySQL/Redis/Git/S3/workspace, referências/hash e backups/restores como obrigação de fase operacional. | AD-5, AD-6, AD-9; PRD atual. |
| Integrações/adapters | Ampliar FR-16–17; adicionar adapters AI/provider, credencial/model policy, executor, Git e deploy; capability/versionamento/Contract Test e não-fallback. | AD-8, AD-10; PRD atual FR-16–17. |
| Código e delivery cliente | Evoluir FR-20/OQ-10 para Git isolado por Project, branch/credential por execução, base/ref CAS, merge protegido; adicionar Deployment Run separado com approvals/secrets/rollout/health/rollback. | AD-10; `0005` §4.6.3 e fases finais. |
| Operação SaaS | Adicionar topo Hetzner, privacidade de dados, egress do runner, updates/backup restore/migrations, escala por plano e telemetria sem metas inventadas. | AD-9; `0006`; `0005` §4.6.3. |
| Assíncrono | Adicionar outbox, envelope canónico, deduplicação/ordenação/idempotência e intake de webhook. | AD-13. |

### NFRs mínimos reorganizados

- **Segurança e isolamento:** contexto derivado; negação sem enumeração; cross-tenant/project, repo errado e object URL/token fora do scope testáveis; browser não atinge CP/runner/queue diretamente.
- **Confinamento de runner:** rootless, efémero, least privilege e resource limits; sem DB/Redis/global S3/Docker socket/segredo global/outro projeto; escape e callback tardio/revogado testáveis.
- **Proteção de secrets:** nenhum secret em browser, prompt, stdout/stderr, logs, Ledger, Artifact, Evidence ou erro/URL; testes de redaction e de rendering BFF.
- **Integridade governada:** Laravel como mutador final; Ledger append-oriented; Baseline preservada; workflow/Gate/Capability versionados; outbox e consumidores idempotentes.
- **Resiliência e operação:** perda de runner não altera facto aceite; retry/timeout/revogação seguros; backup encriptado externo e restore testado antes de produção; dados privados e egress controlado.
- **Portabilidade/escala:** adapters sem lógica de domínio específica; sem fallback; app/workers/runners escaláveis independentemente; números/SLOs apenas após baseline.
- **UX/a11y/auditoria:** Experience atual para erros, filtro e redaction permanece; Evidence mostrada só por modelo autorizado/sanitizado.

## Rastreabilidade que não degrada a leitura do PRD

Definir chaves de fonte estáveis no início, por exemplo `SRC-0006-AD1`…`SRC-0006-AD13`, `SRC-0005-§4.6.3`, `SRC-UX-DESIGN`, `SRC-UX-EXP`, `SRC-SPINE-ADn`, `SRC-PRD-FRn`. Ao fim de cada FR/NFR, colocar `Fontes: [SRC-…]` e, se alterado, `Delta: novo|alterado|preservado`. Isto satisfaz rastreabilidade sem converter o documento inteiro numa matriz redundante. Critérios de aceite devem apontar para os mesmos códigos e testes de segurança devem referir explicitamente a NFR correspondente.

## Métricas recomendadas

Manter SM-1–SM-8 de integridade e acrescentar, sem metas comerciais inventadas:

- cobertura de isolamento: 100% dos cenários de tentativa cross-tenant, cross-project, repo errado, token/URL expirado e runner escape são negados/quarentenados e auditados;
- cobertura de redaction: zero secret nos sinks proibidos nos testes com canários; 100% das apresentações de Evidence passam pelo modelo sanitizado;
- confinamento: 100% das execuções de aceite provam não acesso aos serviços/caminhos proibidos;
- integridade de entrega: 100% dos Deployment Runs aceites têm aprovação, ambiente, secret scope, health result e rollback/elegibilidade registados quando aplicável;
- confiabilidade de recuperação: restauração de backup é testada antes de produção e periodicamente depois, com frequência ainda OQ;
- eficiência observada, não alvo: filas, duração, falhas, retries, cancelamentos, consumo/custo de provider e saturação por Tenant/Project.

Counter-metrics: não maximizar automação, throughput, `PASS`, número de deploys ou volume de Evidence à custa de Gates, isolamento, revisão humana, custo, rollback seguro ou auditabilidade.

## Riscos e respostas que o PRD deve carregar

| Risco | Resposta verificável |
| --- | --- |
| IDOR/cross-tenant ou repositório errado | contexto derivado e fail-closed em cada boundary; testes de enumeração, Git credentials e object URL escopados. |
| Escape/compromisso de runner | Runner Manager separado; rootless, efémero, limites e bloqueio de DB/Redis/S3 global/Docker socket; conformance suite. |
| Segredo em artefacto ou UI | materialização curta, redaction de todos os sinks e evidence presentation model; testes canário. |
| Fallback de provider/executor altera resultado | Capability Matrix/Contract Test; incompatibilidade falha explicitamente e é auditada. |
| Workspace ou Redis torna-se verdade durável | classificação de dados e aceitação no Laravel antes de Ledger/Baseline; perda do runner não muda aceite. |
| Entrega cliente confunde-se com operação SaaS | adapters e pipelines separados; Deployment Run com governança própria. |
| Crescimento prematuro fixa infraestrutura | fases + OQs com gatilhos; topologia inicial separada mas sem prometer Kubernetes/microVM/database-per-tenant. |

## Assumptions e OQs a deixar explícitas

Assumptions aceitáveis: primeira fase opera em Hetzner na topologia já vinculada; um conjunto inicial de adapters será homologado antes de habilitar a capability; metas numéricas derivam de baseline; UX atual de operadora MVP mantém-se até decisão de colaboração. Não assumir provider/modelo, região, CI/CD, IAM, retenção, SLO, billing, ferramentas de observabilidade ou dados de mercado.

| OQ | Owner proposto | Gatilho de decisão |
| --- | --- | --- |
| IdP, MFA e RBAC/ABAC | Architecture + Product/Security | antes de onboarding externo/customer. |
| ID externo/cardinalidades e modelo de dados | Architecture | antes da primeira migration de domínio. |
| Runner protocol, payload, retry/backoff/idempotency | Architecture | antes da implementação do Runner Manager. |
| Primeiro BMAD tag/inventário, provider/model/executor e fixtures | Integration maintainer + Architecture | antes de homologar o primeiro Stage Profile executável. |
| Runtime OCI rootless/host image | Platform/Architecture | antes do build de runner; conformance AD-7 obrigatória. |
| CI/CD, IaC, região/VM sizing, firewall, monitorização e SLO | Platform/Architecture | antes de produção. |
| Região/residência e DR de object storage | Security/Platform | antes do primeiro Tenant de produção. |
| Retenção, eliminação/exportação e tenancy dedicada | Product + Legal/Security | quando requisitos comerciais/compliance o exigirem. |
| microVM/gVisor/Kata, egress proxy, pools dedicados, database-per-tenant | Security/Architecture | quando threat model/baseline ou risco de cliente o exigir. |
| Taxonomia Interaction Mode | Product + UX | antes de expor configuração de modo; reconciliar `Guided/Advanced` e `Guided/Collaborative/Expert`. |
| Billing/plans | Product | antes de qualquer regra de domínio depender de billing. |

## Entradas de memlog que a atualização principal deve produzir

1. `change`: autoridade alterada de SPEC/v0.6 para `0006` + `0005` + UX conforme escopo, porque o SaaS canónico prevalece.
2. `change`: recorte Kernel MVP expandido para SystemBuilder SaaS; exclusões de Git/storage/runners/infra/customer delivery substituídas por capacidades governadas.
3. `decision`: AD-1–AD-13 traduzidos em requisitos observáveis sem redecidir as escolhas já adotadas.
4. `decision`: delivery do SaaS separado de delivery de Project de cliente via adapter e Deployment Run governado.
5. `change`: fontes de verdade MySQL/Redis/Git/S3/workspace tornadas normativas.
6. `change`: requisitos de isolamento, secrets e Runner Manager endurecidos com cenários de teste cross-tenant, leakage, escape e repo errado.
7. `change`: Perfil BMAD ampliado/versionado; segmento `PRD → UX → Architecture → Project Context → Coding/Story` preservado como Gate obrigatório.
8. `assumption` e `event/decision`: fases e todos os OQs acima, cada um com owner, trigger e razão para não escolher tecnologia/meta sem fonte.

