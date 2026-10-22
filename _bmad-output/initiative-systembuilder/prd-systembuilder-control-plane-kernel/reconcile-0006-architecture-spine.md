# Reconciliação arquitetural — 0006 e Architecture Spine

**Objetivo:** extrato vinculativo para a atualização do PRD SaaS. Não substitui a arquitetura: traduz apenas os seus contratos para requisitos de produto verificáveis.

## Fontes e precedência

1. `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf` (v1.0, 2026-09-21): autoridade para tenancy, planes, runners, dados, integrações, infraestrutura e delivery. Referências abaixo usam as secções do PDF.
2. `architecture-systembuilder-control-plane-kernel.md` (2026-10-21): espinha aprovada que operacionaliza 0006. Os ADs são vinculativos; prevalecem sobre exclusões do antigo “Kernel MVP”.
3. PRD anterior: preservar Stage Contract/governança quando compatível, mas remover as exclusões que contradizem as fontes 1–2.

## Reposicionamento obrigatório do PRD

O produto é o **SystemBuilder SaaS multi-tenant** que governa e orquestra BMAD e executores por adapters; não é um novo agente nem substitui BMAD. O escopo passa a incluir Console/BFF, Control Plane Laravel, Execution Plane isolado/Runner Manager, Git por projeto, object storage, adapters AI/provider/executor/Git/deploy e a topologia operacional mínima Hetzner. O Kernel de Stage Contract continua como a camada de governança transversal do Control Plane.

O PRD deve distinguir as fontes de verdade: **MySQL** para estado/metadados/audit governados; **Redis** apenas para fila/cache/coordenação; **Git** para código persistente por projeto; **storage S3 compatível** para artefatos, uploads, exports e backups; **workspace local de runner** descartável. (0006 §§4.3, 12; AD-6.)

## Requisitos/invariantes a tornar verificáveis

| Área de requisito do PRD | Contrato verificável e fonte |
| --- | --- |
| Console e autoridade | Browser usa somente Next.js BFF com sessão segura; exceção direta é apenas URL pré-assinada, curta e limitada a objeto/ação. BFF não detém regras de domínio nem autoridade final. Laravel reautoriza Tenant, Project e ação e é a única autoridade para mutar estado governado, Ledger, Gates e progressão. (0006 §§4.1, 4.2, 5; AD-2, AD-3.) |
| Tenancy e dados | Tenant/Project vêm de membership autenticada no servidor, não de IDs/headers do browser. Registros e consultas scoped falham fechados sem contexto; MySQL inicial é shared-schema com `tenant_id`; tokens de execução, URLs de objeto e credenciais Git são de uma execução/projeto. Testar IDOR e acesso cross-tenant/project. (0006 §§4.3, 5, 22; AD-4.) |
| Workflow governado | Stage Contract, Readiness, Gate, Override/Exceção Formal, Assurance, Capability Matrix, Guardrails, Baseline, impacto e Ledger são declarativos, versionados e append-oriented. Conclusão técnica de runner nunca é aceitação; reabertura preserva baseline; dependentes permanecem inelegíveis enquanto o impacto estiver incompleto/falhar. (0006 §§13–14; AD-5.) |
| Perfil BMAD | O Perfil de Workflow obrigatório e versionado impõe `PRD → UX → Architecture → Project Context → Coding/Story`; cada recomendação, autorização e transição é validada contra a revisão ativa. Artefato existente, agente ou recomendação não salta Gate; exceção só se pré-declarada, aprovada, auditada e limitada a uma transição. (0006 §§13–14, 21; AD-11.) |
| Execução isolada | Laravel autoriza job escopado; Runner Manager cria por tentativa um container/workspace efémero, rootless, sem privileged mode, com limites CPU/RAM/disco/PIDs/timeout e quotas Tenant/Project. Só recebe checkout do projeto, BMAD versionado RO, contexto aprovado e credenciais temporárias de execução/Git/provider. Em retry há nova tentativa limitada; resultado expirado, revogado ou tardio é quarentenado. Laravel valida correlação/política antes de aceitar. (0006 §§7–8, 21–22; AD-7.) |
| Negação explícita ao runner | Runner nunca acede a MySQL, Redis, credenciais S3 globais, Docker socket, APP_KEY, segredos de outro provider/tenant, paths/tokens/outro workspace de projeto; não há acesso runner-to-runner. Workspace é destruído após execução, persistindo só Git e artefatos publicados. Testar runner escape, path/repo errado e destruição sem perda do código Git. (0006 §§7, 9, 11–12, 22; AD-6, AD-7, AD-9.) |
| AI, executor e segredos | Credential Profile, Model Policy, Provider Adapter, Execution Adapter e Capability Matrix são registros/contratos separados e versionados. Credencial é cifrada fora da chave mestre de DB, materializada só como secret temporário e mascarada. Nenhum segredo pode surgir no browser, prompts persistidos, stdout/stderr, logs, Ledger, Artifact, Evidence ou UI. Incompatibilidade falha explicitamente: não há fallback silencioso de provider/model/executor. (0006 §6; AD-8.) |
| Git e artefatos | Cada projeto tem repositório Git isolado; branch/ref e credencial temporária são restritas; commit/diff liga-se a workflow/story/execution e merge protegido depende de Gates. Artefatos têm metadados e referência/hash durável em storage governado. (0006 §§9, 12, 14; AD-6, AD-10.) |
| Eventos/auditoria | Decisões, execuções, provider/model, versão BMAD/guardrails, base commit, resultados e mudanças manuais são rastreáveis de Requirement até Release. Mutações transacionais usam Ledger+outbox; mensagens/callbacks têm envelope canónico, deduplicação, sequência e verificação de webhook (assinatura, replay, scope, idempotência). (0006 §§14, 21; AD-13 — complementar à lista AD-1…AD-11.) |
| Rede e operação | Edge/app públicos; MySQL e Redis exclusivamente em rede privada; hosts de runners separados, egress controlado para AI/Git/registries/Control Plane escopado; progresso chega ao browser apenas via BFF. Backups cifrados fora da VM operacional, restore testável antes e periodicamente em produção. (0006 §§10–12, 15, 19, 22; AD-9.) |
| Delivery | Delivery de projeto de cliente é capability por Deployment Provider Adapter, separada do deploy do próprio SaaS. Environments, SecretSet, approvals, rollout, health-check e rollback são governados e auditáveis; runner de desenvolvimento não recebe por defeito credenciais permanentes de produção. (0006 §§20–21; AD-10.) |

## Restrições AD-1…AD-11 que o PRD não pode contradizer

- **AD-1:** Laravel modular hexagonal: política de negócio em Domain/Application; UI/runner/adapters não decidem Stage, Gate, tenancy, auditoria ou autorização.
- **AD-2:** BFF é a única superfície normal do browser; sessão/capacidade interna não chega ao JavaScript; BFF não vira segundo backend de domínio.
- **AD-3:** Laravel é único autor de estado governado e de autorização final; Runner Manager não decide progressão.
- **AD-4:** scope Tenant é obrigatório em todas as fronteiras e fail-closed; futura DB por tenant muda localização, não semântica de scope.
- **AD-5:** workflow/governança são versionados, declarativos e append-oriented; Gate/Exit Contract exigidos; Override e Exceção Formal são distintos e nunca convertem `BLOCKED` em `PASS`.
- **AD-6:** separar fontes de verdade por classe de dado; workspace/Redis nunca são verdade persistente.
- **AD-7:** execução exclusivamente no Execution Plane isolado e efémero, com credenciais/inputs mínimos e aceitação final pelo Laravel.
- **AD-8:** provider, credencial, modelo, executor e capability desacoplados; redaction integral; incompatibilidade explícita, sem substituição silenciosa.
- **AD-9:** separação de app público, dados privados e egress de runner; escala independente e recuperação comprovável.
- **AD-10:** Git isolado e delivery cliente por adapter governado; não confundir com pipeline de deploy do SaaS.
- **AD-11:** rota BMAD é Perfil versionado e verificável; pré-requisitos ausentes/stale/não validados bloqueiam Coding/Story.

## Fases de produto/operação a refletir

- **MVP operacional Hetzner:** uma VM app (Console/BFF + Laravel), MySQL privado, Redis inicialmente co-localizável por custo mas com plano de separação, VM de runners separada/rootless, S3 para artefatos/backups, Git isolado, backup lógico fora da VM. Não executar código de cliente junto de app/DB. (0006 §§10, 15.1.)
- **Crescimento horizontal:** LB, 2+ app nodes stateless, workers separados, Redis dedicado, pool de VMs de runner/scheduler, telemetria e egress proxy. (0006 §15.2.)
- **Hardening enterprise:** pool dedicado/microVM, DB e storage/keys dedicados, região/infra dedicada, egress estrito/SIEM conforme risco/contrato. (0006 §15.3.)

## Exclusões antigas a retirar/substituir

Não manter como “fora de escopo” a infraestrutura/produção, Git, storage, runners, integrações de repo/deploy ou a plataforma ideia→delivery quando os requisitos acima os exigem. A formulação correta é: o PRD exige contratos e capacidades SaaS; escolhas de implementação ainda não decididas permanecem OQs, sem enfraquecer os invariantes.

## Não-compromissos e Open Questions a manter explícitos

| Open Question / owner | Gatilho de decisão |
| --- | --- |
| IdP, MFA e modelo RBAC/ABAC exato — Architecture/Product | antes de onboarding externo/de cliente; Laravel mantém autoridade final. |
| Provider/executor inicial, método de ligação e fixtures de Contract Test — Product + Architecture | antes de homologar o primeiro Stage Profile executável; não assumir subscrição de consumo como API. |
| Runtime rootless/host image e protocolo Runner Manager, payload/retry/idempotência — Architecture | antes de construir o primeiro runner; provar AD-7 em conformance suite. |
| CI/CD, IaC, região Hetzner, sizing, firewall detalhado, monitoring e SLOs — Architecture/Operations | antes de produção; não escolher Kubernetes nem microVMs no MVP. |
| Residência/DR de Object Storage, retenção, deleção/export e tenancy enterprise dedicada — Product/Architecture/Compliance | antes do primeiro tenant de produção ou requisito contratual. |
| gVisor/Kata/microVM, egress proxy, pool dedicado e DB-per-tenant — Security/Architecture | quando threat model/risco de cliente o exigir; não são compromisso imediato. |
| Provider de billing/planos — Product | antes de funcionalidade de billing; nenhum requisito depende dele agora. |
| Política Interaction Mode — Product/UX | reconciliar `Guided`/`Advanced` da UX com `Guided`/`Collaborative`/`Expert` de 0005; versão de Project independente de autonomia. |

## Rastreabilidade mínima a usar no PRD revisado

Todo FR/NFR criado ou materialmente alterado deve indicar uma destas origens: `0006 §<n>`, `Spine AD-<n>`, UX (DESIGN/EXPERIENCE) ou `0005 §<n>`. Para segurança, os critérios de aceite devem permitir evidência de: (1) ID manipulado/cross-tenant, (2) fuga ou mount do runner, (3) secret leakage em cada superfície proibida, (4) repo/branch/credencial errados, (5) fallback implícito de provider/model/executor, e (6) callback/evento repetido, fora de sequência ou sem correlação.
