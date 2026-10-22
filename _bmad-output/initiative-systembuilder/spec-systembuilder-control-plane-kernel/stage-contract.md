# Stage Contract v0.1

## Contrato genérico

| Secção | Conteúdo obrigatório | Efeito no Kernel |
| --- | --- | --- |
| Identity | `stage_id`, tipo, versão, `project_id`, `workflow_id`, owner, Capability alvo | Identifica a unidade governada. |
| Entry | Inputs obrigatórios/opcionais, Provenance e refs importadas | Determina se Readiness pode iniciar. |
| Readiness | checks, gaps/warnings/blockers e política de Override | Determina resultado e ações elegíveis. |
| Execution | ações, autonomia, permissões, Gates e decisões humanas | Limita trabalho autorizado. |
| Output | Artifact, Evidence, Finding e risco residual | Define resultados demonstráveis. |
| Closure | Exit Contract, aprovações e fechamento | Determina `COMPLETED`. |
| Dependencies | upstream/downstream, materialidade, invalidação, revalidação e reabertura | Determina impacto e preservação histórica. |

Uma secção ou campo obrigatório ausente invalida o contrato antes do uso. Alteração publicada cria versão nova; Stage Revision conserva a versão exata usada. Versão de contrato e número da revisão são identificadores distintos.

## Lifecycle

| Origem | Guarda | Destino | Efeito |
| --- | --- | --- | --- |
| `NOT_STARTED` | início/adoção autorizada | `READINESS` | Regista Inputs e Provenance. |
| `READINESS` | `PASS` ou progressão permitida | `ACTIVE` | Preserva resultado e eventual Override. |
| `READINESS` | pendência humana resolúvel | `AWAITING_USER` | Regista pendência e próxima ação. |
| `READINESS`/`ACTIVE` | blocker | `BLOCKED` | Nega/revoga ação incompatível. |
| `AWAITING_USER` | Input/Decision recebido | `READINESS` | Reavalia checks afetados. |
| `BLOCKED` | blocker removido | `READINESS` | Reavalia antes de retomar. |
| `BLOCKED` | Exceção Formal válida | destino pré-declarado | Preserva `BLOCKED`; limita-se à transição/ações enumeradas. |
| `ACTIVE` | intervenção humana | `AWAITING_USER` | Preserva estado da Execution. |
| `ACTIVE` | outputs candidatos | `READY_TO_CLOSE` | Inicia Exit Contract. |
| `READY_TO_CLOSE` | critério obrigatório falha | `ACTIVE` | Regista gap. |
| `READY_TO_CLOSE` | Exit Contract e Gate aprovados | `COMPLETED` | Cria Baseline. |
| `COMPLETED` | reabertura autorizada | `REOPENED` | Preserva Baseline e cria revisão. |
| `REOPENED` | nova revisão criada | `READINESS` | Reavalia Inputs, Evidence e impacto. |

Transição não declarada é rejeitada e registada. Estados de Stage, Execution e Finding, e Readiness Result, são dimensões independentes. `STALE` não permite progressão automática: requer revalidação.

## Readiness e exceções

| Resultado | Progressão |
| --- | --- |
| `PASS` | Prossegue pela transição declarada. |
| `CONCERNS` | Só prossegue se a política permitir e o risco residual for registado. |
| `FAIL` | Não prossegue, salvo Override do check exato declarado como overrideable. |
| `BLOCKED` | Não tem Override genérico; remove o blocker ou usa Exceção Formal pré-declarada. |

Um `FAIL` só pode ser overrideable quando o Stage Contract nomeia o check e ele é: completude não essencial, ambiguidade não material limitada a hipótese/decisão/Input opcional identificado, ou limiar de qualidade tolerável com impacto delimitado. Nunca são overrideable checks de identidade, ownership/Provenance, autenticação/autorização, Capability/permissão/credencial, Input/Output/Evidence/aprovação/Gate/critério/dependência obrigatórios, isolamento/integridade ou Finding de segurança bloqueante.

Override regista ator, tempo, check, resultado original, motivo, política, aprovação, decisor, escopo, validade e risco residual. Para `FAIL`, a aprovação é anterior à transição e o escopo cobre somente essa transição. Nunca concede Capability ou permissão ausente.

Exceção Formal de `BLOCKED` não é Override. É declarada no Stage Contract ou Perfil de fluxo antes da avaliação com blocker, Stage Profile/versão, transição, ativação, duração, aprovador, Evidence, controlo compensatório, ações permitidas e encerramento. Só cobre indisponibilidade temporária de Artifact upstream ou validação não relacionada a segurança, com Evidence substituta e controlo verificáveis. É proibida para identidade, isolamento, autorização, Capability/permissão/credencial, segurança bloqueante, integridade de Evidence e requisito legal/regulatório. Não satisfaz o check, não reclassifica para `PASS` e expira/encerra no limite declarado.

## Impacto downstream

Cada dependência declara revisão/Evidence upstream de referência, elementos observados, materialidade, invalidação e ação de revalidação. Para cada mudança upstream, a primeira regra verdadeira decide:

1. `STALE`: invalidação declarada, link `invalidates`, ou Input/Evidence/aprovação/Gate/critério obrigatório do dependente ficou inválido.
2. `REQUIRES_REVIEW`: atingiu elemento observado, materialidade é indeterminável, ou a declaração da relação falta.
3. `unchanged`: nenhum elemento observado foi atingido e não há invalidação.

A análise regista gatilho, delta/revisões, regra, motivo, Evidence e próxima ação; substitui apenas a disposição da relação da nova análise, sem apagar análise ou Artifact anteriores. `REQUIRES_REVIEW` bloqueia autorização/fechamento dependente até decisão; `STALE` exige nova Stage Revision, Readiness e revalidação. Artefactos downstream não são apagados automaticamente.

## Stage Profiles

| Perfil | Entry/Readiness | Exit Contract |
| --- | --- | --- |
| Ideation/Brainstorm | Tenant, Project, intenção/fonte e Provenance; falta de identidade/autorização é `BLOCKED`, ambiguidade material é `FAIL`. | Brainstorm, product brief PRD-ready, hipóteses/lacunas explícitas e Assurance leve. |
| PRD | Brief/equivalente, utilizadores, escopo, restrições, dependências, integrações, regras e Provenance. | PRD aprovado, Requirements identificados, critérios/métricas/regras/dependências, contradições materiais tratadas e âncoras de rastreabilidade. |
| Coding/Story | Story estável, critérios testáveis, dependências, branch/base, execution policy e refs aplicáveis a Requirement, Architecture, UX, segurança e testes. | Build, tests e checks aplicáveis; critérios satisfeitos; nenhum Finding bloqueante; diff/commit correlacionado à Execution. |

Coding/Story valida `creation/adoption → adversarial review → security review aplicável → Readiness` antes de desenvolvimento e `tests → code review → security review aplicável → final verification → closure` depois; Gate não aplicável regista a regra usada.
