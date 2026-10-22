<!-- bmad:context -->
<!-- Verified 2026-10-22 against b672eb1. Managed by bmad-project-context; edits inside this block are replaced on refresh. Keep anything you want preserved outside the markers. -->

## SystemBuilder

SystemBuilder é um SaaS multi-tenant que governa workflows de desenvolvimento assistido por IA. O Console/BFF Next.js é uma superfície de apresentação; o Control Plane Laravel mantém a autoridade de domínio e o Execution Plane executa trabalho não confiável isoladamente. Os contratos aprovados vivem no PRD, Architecture Spine e artefactos UX abaixo.

## Policy

- Preserve a fronteira hexagonal: regras de negócio ficam em `Domain`/`Application`; sistemas externos só são alcançados por ports da aplicação implementados em `Infrastructure`.
- Aplique SOLID no backend. Controllers adaptam HTTP para casos de uso; não alojam regras de negócio, orquestração de persistência ou transições de governança.
- Valide input na camada dedicada de Request/Form, tanto no backend como no frontend. A validação de UI melhora a experiência, mas Laravel continua a validar, reautorizar e aplicar todas as regras de domínio.
- Use migrations versionadas e imutáveis: nunca altere uma migration já existente; crie uma nova migration para cada evolução de schema.
- Faça o browser comunicar apenas com o Console/BFF. Laravel reautoriza Tenant, Project e ação e é a única autoridade que cria ou altera estado governado, Gates, Baselines, Ledger ou autorizações de Execution.
- Derive Tenant e Project de membership autenticada em cada fronteira; nunca aceite IDs, headers ou query strings do browser como concessão de escopo. Falhe fechadamente e não revele a existência de recursos cross-scope.
- Mantenha o Execution Plane isolado: Runner Manager e runners recebem somente jobs já autorizados e nunca decidem Gate, autorização, progressão ou estado aceite.
- Modele Workflows, Stage Contracts/Profiles, Capability Matrix, Guardrails e decisões materiais como contratos versionados e append-oriented. Correções criam evento ou revisão; não reescrevem Baselines, Evidence, decisões ou factos aceites.
- Não avance uma Stage por sucesso técnico do runner: a progressão exige Gate e Exit Contract declarados. `BLOCKED` nunca vira `PASS` por Override; Exceção Formal é pré-declarada, limitada, auditada e distinta de Override.
- No perfil BMAD obrigatório, bloqueie Coding/Story até PRD, UX, Architecture e Project Context estarem presentes, validados e não stale.
- Não implemente fallback implícito entre provider, modelo, executor ou adapter. Combinação não homologada ou capability ausente falha explicitamente.
- Nunca persista ou apresente segredos em browser, prompts, stdout/stderr, logs, Ledger, Artifact ou Evidence. Runners recebem apenas credenciais temporárias e estritamente escopadas.

## Where things are

- Requisitos e critérios de aceitação: `_bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md`
- Limites estruturais e decisões AD-1–AD-13: `_bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md`
- Comportamento, estados e acessibilidade do Console: `_bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/EXPERIENCE.md`
- Tokens e regras visuais do Console: `_bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/DESIGN.md`; ao implementar UI, use também `docs/theme.css`.

## Running and verifying

- Execute `uv run _bmad/scripts/setup_check.py` antes de trabalho BMAD; ele diagnostica instalação/configuração pendente de skills, mas não valida comportamento da aplicação.
- Não afirme validação de build, testes ou integração: `package.json` não possui scripts e ainda não há comandos de aplicação verificados. Quando os componentes forem semeados, estabeleça os comandos canónicos nos manifests/CI de Console, Control Plane e Runner Manager antes de os documentar aqui.
- Para alterações de governança, teste cenários de negação cross-tenant/cross-project, ausência de fuga de segredo, não-bypass de Gate/Perfil BMAD e contenção de runner; para Console, verifique WCAG 2.2 AA, teclado, quatro locales e mobile apenas para consulta.

## Conventions that differ from defaults

- MySQL é a verdade para metadata e auditoria governadas; Redis é somente cache/fila/coordenação; Git é a verdade do código de cada Project; storage S3 compatível guarda artefactos duráveis; workspace de runner é descartável.
- Cada mutação governada persiste factos de Ledger e outbox atomicamente. Mensagens assíncronas carregam correlação e sequência imutáveis; consumidores deduplicam e rejeitam sequência ou transição inválida.
- Evidence para UI é um modelo de visualização autorizado e sanitizado, nunca logs brutos de runner. Estado, regra, razão e risco residual são texto; cor ou score não bastam.
- Ações críticas pertencem a desktop/laptop. Em mobile, exponha somente consulta segura de estado, razão, Evidence autorizada e rastreabilidade.
- Reabertura preserva a Baseline e cria uma Stage Revision ligada por `supersedes`; impacto downstream incompleto ou falho mantém autorização e fecho dependentes inelegíveis.

## Known pitfalls

- Não trate Redis, logs ou workspace de runner como fonte de verdade, nem aceite artefacto/código por persistir apenas no runner.
- Não entregue ao runner MySQL, Redis, Docker socket, `APP_KEY`, credenciais S3 globais, segredos de outro provider ou paths/tokens de outro Project.
- Não aceite callback duplicado, expirado, revogado ou fora de sequência; coloque resultados candidatos em quarentena até Laravel validar correlação e política.
- Não revele conteúdo ou existência de Evidence restrita; previews passam por classificação e redação, e URLs pré-assinadas são curtas e limitadas a objeto/ação autorizados.
- Não misture delivery de código de cliente com deployment do SaaS: Deployment Runs têm adapters, aprovações, ambientes, secrets, health checks e rollback próprios.

<!-- /bmad:context -->
