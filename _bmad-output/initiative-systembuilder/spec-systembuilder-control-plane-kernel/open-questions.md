# Open Questions

## Registo histórico do Kernel — IDs imutáveis

Estes IDs e respetivos assuntos foram publicados antes da revisão SaaS. Mantêm a identidade e continuam abertos até que uma decisão versionada registe a respetiva disposição; uma incerteza nova nunca reutiliza um destes IDs.

| ID | Decisão necessária | Owner e gatilho | Disposição |
| --- | --- | --- | --- |
| OQ-1 | Identidade, cardinalidade e ownership físico entre Stage, Stage Revision, Workflow, Execution, Artifact, Evidence, Decision, Gate e Finding. | Architecture; antes da primeira migration de modelo de dados. | Aberta; OQ-18 complementa identidade externa e autorização. |
| OQ-3 | Mecanismos de persistência, versionamento, retenção, exportação, redaction e recuperação para Ledger e Baseline. | Architecture + Compliance; antes de persistência de produção. | Aberta; OQ-22 detalha dados/object storage. |
| OQ-5 | Contrato mínimo Control/Execution para mensagens, falhas, idempotência, revogação e entrega de Evidence. | Architecture; antes do primeiro runner. | Aberta; OQ-20 detalha runtime e protocolo Runner Manager. |
| OQ-6 | Como credenciais, código, prompts, logs e Evidence são escopados, cifrados, redigidos, retidos e destruídos. | Security + Architecture; antes de processar segredos ou dados de produção. | Aberta; os invariantes de não divulgação já são aprovados. |
| OQ-7 | Política de evolução para Contract, Profile, Workflow, Adapter e Capability Matrix. | Architecture; antes de homologar versões incompatíveis. | Aberta. |
| OQ-8 | Versão/tag/commit BMAD e inventário mínimo de operações/Contract Test do primeiro build. | Product + Architecture; antes da primeira homologação BMAD. | Aberta; não é o identificador de deployment introduzido pela revisão SaaS anterior. |
| OQ-9 | Provider/executor inicial e Capabilities obrigatórias/opcionais. | Product + Architecture; antes da primeira homologação executável. | Aberta; OQ-19 concretiza modelo, ligação e fixtures. |
| OQ-10 | Integração mínima de repository, branch/base, build, testes e reviews para Coding/Story real. | Architecture + Product; antes de Coding/Story executar contra Git. | Aberta. |
| OQ-11 | Evidence, fixtures e checks adicionais mínimos por Stage Profile. | Product + Architecture; antes de homologar cada Profile. | Aberta. |
| OQ-13 | SLOs e metas além das métricas de integridade após baseline instrumentada. | Operations + Product; após baseline instrumentada e antes de SLO externo. | Aberta. |
| OQ-14 | `IMPORT_ADOPTION` é Gate próprio ou ação dentro de Entry Contract. | Product + Architecture; antes de implementar adoção. | Aberta. |
| OQ-15 | Interface inicial para pendências, Gate, histórico e impacto. | UX + Product; antes de implementar a superfície operacional completa. | Aberta. |
| OQ-16 | Beachhead, segmento ou cenário inicial. | Product; antes de compromissos comerciais. | Aberta. |
| OQ-17 | Requisitos de deploy, operação, observabilidade e suporte após o MVP. | Architecture + Operations; antes de produção. | Aberta; OQ-21, OQ-25 e OQ-26 separam escolhas SaaS, delivery de cliente e comercialização. |

## Incertezas SaaS adicionadas nesta revisão

| ID | Decisão necessária | Owner e gatilho |
| --- | --- | --- |
| OQ-18 | IdP, MFA e modelo RBAC/ABAC. Laravel mantém a autorização final. | Architecture + Product; antes de onboarding externo. |
| OQ-19 | Primeiro provider/model/executor, método de ligação e fixtures de Contract Test. | Product + Architecture; antes de homologar o primeiro Stage Profile executável. |
| OQ-20 | Runtime rootless, imagem de host e contrato Runner Manager para payload, retry, cancelamento e idempotência. | Architecture; antes do primeiro runner. |
| OQ-21 | CI/CD, IaC, região Hetzner, sizing, firewall, observabilidade e SLOs. | Architecture + Operations; antes de produção. |
| OQ-22 | Residência e DR do object storage, retenção, deleção/export e classificação de dados. | Product + Architecture + Compliance; antes do primeiro Tenant de produção ou obrigação contratual. |
| OQ-23 | Gatilho para gVisor/Kata/microVM, egress proxy, pool dedicado e database-per-tenant. | Security + Architecture; quando threat model ou risco de cliente o exigir. |
| OQ-24 | Taxonomia de Interaction Mode que reconcilia UX e fluxo de origem, independente de Autonomy Policy. | Product + UX; antes de expor configuração geral. |
| OQ-25 | Primeiro Deployment Provider Adapter e política de ambientes/aprovações. | Product + Architecture; antes de delivery de cliente fora de cenário controlado. |
| OQ-26 | Modelo comercial, billing e quotas. | Product; antes de funcionalidade comercial. |

## Transição de referências SaaS reatribuídas

A primeira re-derivação SaaS reutilizou OQ-1–OQ-9 para novas incertezas. Essas referências transitórias não alteram os IDs históricos acima: ao interpretar material produzido nessa revisão, use a migração abaixo. Referências novas devem usar apenas os IDs de destino.

| Referência SaaS transitória | ID estável de destino | Assunto preservado |
| --- | --- | --- |
| OQ-1 | OQ-18 | IdP, MFA e RBAC/ABAC. |
| OQ-2 | OQ-19 | Primeiro provider/model/executor, ligação e fixtures de Contract Test. |
| OQ-3 | OQ-20 | Runtime rootless, imagem de host e protocolo Runner Manager. |
| OQ-4 | OQ-21 | CI/CD, IaC, região/sizing/firewall, observabilidade e SLOs. |
| OQ-5 | OQ-22 | Residência/DR de object storage, retenção, deleção/export e classificação. |
| OQ-6 | OQ-23 | Gatilho para sandbox reforçada, egress e tenancy dedicada. |
| OQ-7 | OQ-24 | Taxonomia de Interaction Mode. |
| OQ-8 | OQ-25 | Deployment Provider Adapter e política de ambientes/aprovações. |
| OQ-9 | OQ-26 | Modelo comercial, billing e quotas. |

## Invariantes resolvidos

Override de `FAIL`, Exceção Formal de `BLOCKED`, classificação determinística de impacto, preservação de Baseline, fronteira Console/BFF–Laravel, isolamento de runner, separação das fontes de verdade e envelopes assíncronos são requisitos aprovados, não Open Questions. Provider/runtime/adapter, IAM, retenção, infraestrutura e deployment continuam escolhas concretas abertas e não devem ser apresentados como invariantes resolvidos.
