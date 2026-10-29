# Impacto em Requisitos e Futuros Tickets

## Cobertura por capability

| Capability | Requisitos impactados | Arquitetura e UX a preservar |
| --- | --- | --- |
| CAP-1–CAP-7 | FR-4–FR-6, FR-13, FR-15–FR-16 | AD-1, AD-3, AD-5, AD-11; UJ-1–UJ-3 |
| CAP-8, CAP-21 | FR-8, FR-12–FR-13; NFR-5 | AD-5, AD-13; UJ-4 |
| CAP-9 | FR-7; NFR-4 | AD-8; UJ-5 |
| CAP-10–CAP-11 | FR-6, FR-15 | AD-5, AD-11; UJ-1–UJ-2b |
| CAP-12, CAP-17 | FR-8–FR-9; NFR-3–NFR-5 | AD-3, AD-7, AD-9, AD-13; UJ-1 |
| CAP-13–CAP-14 | FR-4, FR-16 | AD-5, AD-11; UJ-1, UJ-3 |
| CAP-15 | FR-1–FR-2; NFR-1, NFR-4 | AD-2–AD-4; UX Foundation and Accessibility Floor |
| CAP-16 | FR-2–FR-3; NFR-2, NFR-7 | AD-2, AD-3; todos os UJ-1–UJ-5 |
| CAP-18 | FR-10–FR-11; NFR-2–NFR-3 | AD-6, AD-10 |
| CAP-19 | FR-14 | AD-12 |
| CAP-20 | FR-17; NFR-8 | AD-10; UJ-6 |

## Impacto da atualização

- A fronteira anterior de Kernel isolado é substituída pelo escopo SaaS aprovado. CAP-15–CAP-21 são IDs novos e estáveis; CAP-1–CAP-14 preservam IDs e recebem apenas redação reforçada.
- Os companions existentes de Stage, Workflow, Assurance, auditoria, referência de arquitetura e questões mantêm autoridade no seu tema. PRD, Architecture Spine, UX DESIGN/EXPERIENCE e `AGENTS.md` são fontes normativas referenciadas pela SPEC; nenhum companion local as substitui.
- A afirmação anterior de que UX final, infraestrutura física e delivery de cliente estavam fora do escopo é retirada. Tecnologias e escolhas operacionais exatas continuam Open Questions, não decisões implícitas.

## Inventário verificado da árvore de tickets

Existe uma árvore publicada em `../tickets.toml`, com sete breakdowns de epic. Esta correção apenas documenta o impacto: não altera corpos de tickets, estados, dependências nem estado GitHub. O único `epic-*/tickets.toml` atualmente presente é o do primeiro epic; os restantes breakdowns existem como documentos de epic e são referenciados pela árvore da iniciativa.

| Epic existente | CAP coverage declarada | Evidência desta revisão |
| --- | --- | --- |
| `epic-tenant-authority-foundation` | CAP-15, CAP-16 | Preservar scope derivado, BFF/Laravel e Evidence sanitizada. |
| `epic-ledger-traceability-integrity` | CAP-8, CAP-21 | Preservar Ledger/outbox atómico, correlação, sequência e deduplicação. |
| `epic-governed-workflow-kernel` | CAP-1–CAP-7, CAP-10, CAP-11, CAP-13, CAP-14 | Preservar Gates, Baseline, impacto e perfil BMAD sem bypass. |
| `epic-capability-execution-authorization` | CAP-9, CAP-12, CAP-15 | Preservar homologação explícita, autorização e quarentena de resultados. |
| `epic-isolated-execution-artifacts-context` | CAP-15, CAP-17, CAP-18, CAP-19 | Preservar contenção de runner, Git/Artifact durável e Constitution. |
| `epic-governed-console-audit` | CAP-8, CAP-16 | Preservar apresentação autorizada/sanitizada e rastreabilidade. |
| `epic-client-deployment-governance` | CAP-15, CAP-20 | Preservar Deployment Run separado do SaaS; adapter concreto continua condicionado a OQ-25. |

## Fatias publicadas e futuras

A árvore publicada acima é a referência de slicing atual. Uma futura alteração de tickets com `bmad-ticket` deve referenciar estes capability IDs e preservar a Evidence indicada; esta SPEC companion não autoriza mutação de tickets.

| Fatia proposta | Capability IDs | Evidence obrigatória |
| --- | --- | --- |
| Tenancy e autoridade BFF | CAP-15, CAP-16 | Negação cross-scope, fronteira browser→BFF, Evidence sanitizada, WCAG/teclado/locales/mobile de consulta. |
| Núcleo de Workflow governado | CAP-1–CAP-7, CAP-10–CAP-14 | Testes de lifecycle e Gate; sem bypass genérico de `BLOCKED`; cenários de pré-requisito BMAD e importação/adoção. |
| Ledger e integridade assíncrona | CAP-4, CAP-8, CAP-21 | Outbox atómico, rejeição de correlação/sequência, reconstrução de auditoria, sem depender de logs brutos de runner. |
| Homologação de capability e integração | CAP-9, CAP-12 | Contract Test, incompatibilidade explícita, revogação/expiração, quarentena e reconciliação de candidatos. |
| Execução isolada e outputs duráveis | CAP-17, CAP-18, CAP-19 | Contenção de runner, scopes temporários, isolamento Git/ref, durabilidade de Artifact, Provenance de Constitution. |
| Delivery de cliente | CAP-20 | Aprovação por adapter/ambiente, scope de segredo, health check e rollback; sem acoplamento à pipeline SaaS. Demonstração de referência só após OQ-25. |

## Piso de aceite para cada ticket de implementação

- Cite cada FR/NFR, AD e `CAP-N` aplicável acima.
- Teste negação cross-Tenant/cross-Project, não divulgação de segredo, não-bypass de Gate/Perfil e contenção de runner sempre que o ticket tocar governação ou execução.
- Não afirme validação de build, integração ou testes da aplicação até existirem comandos canónicos nos manifests/CI dos componentes.
