# Prompt — reconciliar o PRD com a arquitetura SaaS canónica

Use o workflow **`bmad-prd`** para atualizar — não apenas resumir — o PRD existente do SystemBuilder.

## Objetivo

Substituir o atual enquadramento de “Control Plane Kernel MVP” por um PRD coerente para o **SystemBuilder SaaS**, mantendo o Kernel como núcleo governado. O resultado deve poder orientar o fatiamento posterior sem contradizer a arquitetura.

## Precedência de fontes

1. **Autoridade arquitetural:** `docs/0006_02-Arquitetura_Referencia_Painel_BMAD.pdf`.
2. **Fluxo de produto/processo:** `docs/0005_01-Fluxo_Desenvolvimento_Painel_BMAD.pdf`.
3. **Contrato UX atual:** `_bmad-output/initiative-systembuilder/ux-systembuilder-control-plane-kernel/{DESIGN.md,EXPERIENCE.md}`. Preserve as suas decisões fechadas quando compatíveis; amplie-as somente quando a superfície SaaS exigir.
4. **PRD atual a reconciliar:** `_bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md`.
5. **Espinha arquitetural já aprovada:** `_bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md`.

Em conflito, não preserve silenciosamente a limitação do PRD atual: 0006 prevalece para arquitetura, tenancy, runners, dados, integrações, infraestrutura e delivery. Registe no memlog toda decisão, mudança de escopo, requisito retirado ou Open Question criada.

## Requisitos para o PRD atualizado

- Reposicionar o produto como SaaS multi-tenant com Console/BFF, Control Plane, Execution Plane isolado, Runner Manager, Git por projeto, artefatos/object storage, adapters de AI/provider/executor e adapters de deploy.
- Converter em requisitos verificáveis os invariantes da arquitetura: contexto Tenant/Project derivado no servidor; browser fala apenas com BFF; Laravel é autoridade final; runners efémeros/rootless/least-privilege; sem acesso de runner a DB, Redis, Docker socket, segredos globais ou outro projeto; segredos nunca aparecem no browser, prompts, stdout/stderr, logs, Ledger, Artifact ou Evidence; nenhuma troca silenciosa de provider/model/executor; workspace não é fonte de verdade.
- Preservar e integrar, quando compatíveis, Stage Contract, Readiness, Gate, Override/Exceção Formal, Assurance, Ledger append-oriented, Baseline/reabertura, impacto downstream, rastreabilidade e Capability Matrix do PRD atual.
- Tornar o fluxo BMAD `PRD → UX → Architecture → Project Context → Coding/Story` um Perfil de Workflow versionado e verificável; não permitir que agente, artefato existente ou recomendação salte um Gate.
- Separar explicitamente: dados governados em MySQL, coordenação em Redis, código em Git, artefatos/backups em storage S3 compatível e workspaces de runner descartáveis.
- Distinguir deploy do próprio SaaS de deploy de um projeto de cliente; delivery de cliente é uma capability por adapter, com approvals, secrets e rollback próprios.
- Explicitar fases: MVP de arquitetura operacional no Hetzner; crescimento horizontal; hardening enterprise. Não comprometer Kubernetes, microVMs, database-per-tenant, provider inicial, CI/CD, IAM, retenção ou SLOs sem decisão; convertê-los em OQs com owner e gatilho.
- Revisar usuários, jornadas, escopo/não-escopo, FRs, NFRs, métricas, riscos, assumptions e OQs. Eliminar contradições como “infraestrutura/produção/Git fora de escopo” quando o novo produto as exige.

## Critérios de aceite da atualização

- Cada FR/NFR novo ou alterado é rastreável a 0005, 0006, UX ou à spine.
- O PRD não escolhe uma implementação que contradiga `AD-1` a `AD-11`.
- Requisitos de segurança permitem testar isolamento cross-tenant, secret leakage, runner escape e acesso a repositório errado.
- O produto não se torna um substituto do BMAD: ele governa e orquestra BMAD e executores por adapters.
- Lacunas reais permanecem como assumptions ou OQs; não inventar provider, regiões, ferramentas CI/CD, dados comerciais ou metas sem fonte.
- Execute o Reviewer Gate do `bmad-prd` e entregue o PRD e memlog atualizados.
