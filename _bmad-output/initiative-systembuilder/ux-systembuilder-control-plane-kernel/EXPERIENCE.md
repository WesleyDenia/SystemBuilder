---
name: SystemBuilder Control Plane Kernel
description: Behavioral UX contract for the responsive internal web console that governs Stage progression, exception handling, revision, impact, and auditability.
status: final
created: 2026-10-14
updated: 2026-10-15
sources:
  - ../prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md
design: DESIGN.md
---

# SystemBuilder Control Plane Kernel — Experience Spine

> Contrato comportamental do console web interno. `DESIGN.md` é a referência visual baseada em shadcn/ui. Esta spine não substitui regras do PRD: uma ação só é apresentada ou executada quando a política, Gate, Capability e permissões aplicáveis a tornam elegível.

## Foundation

O produto começa como console web interno, construído sobre shadcn/ui. Desktop e laptop suportam o fluxo completo de operação; telemóvel é responsivo para consulta de estado, razão, Evidence e rastreabilidade, sem ações críticas de progressão. O modo da superfície é **Operate**: a tarefa prioritária é acompanhar Readiness e desbloquear uma Stage pela próxima ação elegível.

O MVP tem uma pessoa operadora por Project, que também consulta a auditoria do seu próprio trabalho. Quando a política da Stage concede essa autoridade, essa pessoa confirma a aprovação de Override ou Exceção Formal no mesmo console. A decisão não autoautoriza condições proibidas: `BLOCKED` continua sem Override genérico, e Gate, Capability, permissão e risco continuam guardas obrigatórias. `Interaction Mode` e `Autonomy Policy` permanecem dimensões persistidas e independentes, com default `Guided` + `Supervised` (PD-10, FR-22).

O console suporta pt-PT, pt-BR, inglês e espanhol, além de tema claro e escuro. O idioma selecionado determina a formatação local de datas, horas e números; identificadores, versões, políticas e valores governados preservam a sua forma canónica. Se não houver tradução, o conteúdo original continua visível com um aviso. A preferência do sistema para reduzir movimento elimina animações não essenciais. `DESIGN.md` é a referência de identidade visual; esta spine descreve comportamento e informação.

## Information Architecture

No desktop/laptop, as superfícies seguintes formam um console navegável; a entrada operacional padrão é Readiness da Stage Revision em curso. Em mobile, elas preservam consulta e ligações de rastreabilidade, mas não expõem a execução de ações críticas.

| Superfície | Entrada | Propósito | Saídas/navegação exigidas |
|---|---|---|---|
| Contexto de Project | Seleção/autorização de Project. | Delimitar Tenant e Project do trabalho governado. | Lista ou acesso à Stage Revision dentro do escopo autorizado; não revelar recursos de outro escopo. |
| Detalhe da Stage Revision | Project, Baseline, evento ou relação de rastreabilidade. | Exibir identidade, versão de Stage Contract/Workflow, estado, Inputs, ações elegíveis e vínculos. | Readiness, Gate, Execution, fechamento, revisão, impacto e trilha de auditoria relacionados. |
| Readiness | Início/retomada de Stage Revision ou reavaliação. | Exibir resultado único, checks, gaps, warnings, blockers, Evidence e próxima ação. | Ação permitida, `AWAITING_USER`, `BLOCKED`, solicitação de Override quando elegível, ou retorno ao detalhe. |
| Gate e fechamento | Transição que exige Gate ou `READY_TO_CLOSE`. | Mostrar critérios, Evidence, resultado, ator/origem, timestamp e o Exit Contract. | Detalhe de Gate, Baseline criada ou retorno a `ACTIVE` com gap registrado. |
| Decisão de Override | Um `CONCERNS` ou `FAIL` nominalmente elegível. | Registrar e tornar verificáveis motivo, política, aprovação, escopo, validade e risco residual, preservando a condição original. | Estado da aprovação e somente a transição autorizada. |
| Resolução de `BLOCKED` | Readiness ou Stage Revision bloqueada. | Explicar o blocker e distinguir remoção do blocker de Exceção Formal pré-declarada. | Reavaliação depois de remover o blocker, ou detalhes da exceção formal elegível; nunca Override genérico. |
| Baseline, reabertura e linhagem | Stage `COMPLETED` ou pedido de alteração upstream. | Preservar a Baseline legível e iniciar nova Stage Revision ligada por `supersedes`. | Nova revisão em `READINESS`, Inputs herdados/alterados, Gates/ aprovações a revalidar e análise de impacto. |
| Impacto downstream | Mudança material upstream ou nova Baseline. | Exibir exatamente uma disposição por relação: `unchanged`, `REQUIRES_REVIEW` ou `STALE`, com motivo e próxima ação. | Dependente afetado, revisão/evidência comparada, regra aplicada e caminho de revalidação. |
| Ledger e rastreabilidade | Baseline, Decision, Requirement, Artifact, Evidence, Gate ou Finding. | Reconstruir fatos e cadeia causal sem logs brutos do runner. | Pesquisa por ID/texto, consulta preservável por URL, relações expansíveis, comparação de revisões/Baselines e navegação entre Ledger e Traceability. |
| Capability e homologação | Combinação BMAD/provider/executor ou Contract Test. | Inspecionar Capability, limitações, versão e Evidence de Contract Test para determinar se a combinação é elegível. | Registo de decisão/homologação e retorno à ação governada; combinação incompatível não recebe fallback silencioso. |

Fechamento de superfície: os fluxos abaixo cobrem Readiness, Gate, Override, `BLOCKED`, impacto, reabertura e auditoria. Ledger e Traceability cobrem consulta em volume pela interação declarada nesta spine.

## Responsive & Platform

| Contexto | Comportamento |
|---|---|
| Desktop/laptop | Fluxo completo: criar/adotar Stage Revision, autorizar Execution, confirmar ações elegíveis, fechar, reabrir e navegar por auditoria. |
| Mobile | Consulta responsiva de identidade, estado, razão, Evidence autorizada, Gate, histórico e próxima ação. Não permite aprovar, autorizar Execution, fechar, solicitar/aceitar Override ou ativar Exceção Formal. |
| Tema | Claro e escuro, herdados do shadcn/ui; o estado textual é idêntico nos dois temas. |
| Idioma e formato | pt-PT, pt-BR, inglês e espanhol. O idioma selecionado aplica os seus formatos locais a datas, horas e números; identificadores, versões, políticas e valores governados preservam sua forma canónica. Conteúdo sem tradução mostra o original com aviso. |

## Voice and Tone

A cópia deve ser factual, explicável e orientada à ação elegível. Não deve minimizar condição, esconder exceção, prometer conclusão técnica ou apresentar uma ação como aprovada quando a política não a autoriza.

| Fazer | Evitar |
|---|---|
| “`BLOCKED`: a transição não é elegível. Remova o blocker ou consulte uma Exceção Formal aplicável.” | “Continuar mesmo assim” ou “Ignorar blocker”. |
| “`FAIL` preservado. Override permitido somente para o check e transição declarados.” | “Falha aceita” sem resultado original, regra, aprovação e risco residual. |
| “`REQUIRES_REVIEW`: decisão de revisão necessária antes da próxima autorização ou fechamento dependente.” | “Sem alteração” quando faltam dados para classificar. |
| “Baseline da revisão 3 preservada; a revisão 4 será reavaliada.” | “Editar a Stage concluída”. |
| “Ação inelegível: a política, Capability ou Gate exigido não está satisfeito.” | Um erro genérico que não explica a limitação de governança. |

Identificadores, versões, fontes de Evidence, ator/origem, timestamp, regra/política e motivo devem permanecer disponíveis para inspeção; uma frase de resumo não os substitui.

## Component Patterns

As regras visuais de cada nome estão em `DESIGN.md.Components`. Estes padrões são comportamentais.

| Componente | Onde | Comportamento obrigatório |
|---|---|---|
| `readiness-summary` | Readiness e detalhe da Stage Revision. | Mostra um único resultado entre `PASS`, `CONCERNS`, `FAIL` e `BLOCKED`, explicação, Evidence e ação permitida; não é score opaco. |
| `check-result-row` | Readiness e Gate. | Identifica check/critérios, resultado, regra, Evidence e razão. A nova avaliação acrescenta contexto; não substitui o resultado anterior. |
| `gate-record` | Gate, fechamento e auditoria. | Identifica critérios avaliados, Evidence, resultado, ator/origem e timestamp; Finding bloqueante impede a transição correspondente até o tratamento formal permitido. |
| `override-record` | Decisão de Override e auditoria. | Só aparece quando a condição e a transição são permitidas pela política. No MVP, a mesma pessoa operadora confirma a decisão quando a política lhe confere autoridade; o registo preserva resultado original, check alvo, motivo, base de política, decisor, escopo, validade e risco residual. |
| `blocked-resolution` | `BLOCKED`. | Explica blocker, proíbe a ação de Override genérico e oferece apenas os caminhos elegíveis: remoção do blocker ou Exceção Formal pré-declarada aplicável. |
| `formal-exception-record` | `BLOCKED` com exceção aplicável. | É distinto de `override-record`; exibe código do blocker, transição, condição, duração, autoridade, Evidence substituta, controlo compensatório, ações permitidas e encerramento. A pessoa operadora só confirma a exceção se a política permitir. Não muda o resultado original de `BLOCKED`. |
| `closure-checklist` | `READY_TO_CLOSE`. | Para cada critério aplicável, mostra `satisfied`, `not_satisfied` ou `not_applicable`, justificativa e Evidence. Critério obrigatório `not_satisfied` impede `COMPLETED`. |
| `revision-lineage` | Baseline/reabertura. | Separa Baseline imutável da nova Stage Revision; mostra motivo, ator, tempo, `supersedes` e Inputs herdados ou alterados. |
| `downstream-impact-relation` | Impacto. | Exibe uma e apenas uma disposição por relação com gatilho, delta/revisões comparadas, regra, motivo, Evidence e próxima ação. O conjunto é agrupado nesta ordem: `STALE`, `REQUIRES_REVIEW`, `unchanged`; as duas primeiras disposições criam uma pendência navegável para a revisão dependente. |
| `audit-event-row` | Ledger. | Mostra evento append-oriented com Tenant, Project, ator/origem, timestamp, tipo, entidade alvo e correlação causal quando aplicável. Correções surgem como novo evento/versão. A lista aceita pesquisa por ID/texto, filtros de período, tipo de evento, entidade, Stage Revision/Baseline, ator/origem e resultado; ordena por tempo e pagina por cursor. |
| `traceability-explorer` | Auditoria. | A partir de Requirement, Decision, Baseline, Artifact, Evidence, Gate ou Finding, permite recuperar entidades e eventos relacionados sem depender de logs brutos. As relações aparecem numa lista/árvore expansível com breadcrumbs, ligação “abrir no Ledger” e comparação lado a lado de duas revisões ou Baselines; o MVP não usa grafo. |
| `governance-status-message` | Atualizações assíncronas e ações de governança. | Anuncia início/progresso/conclusão de análise e reavaliação como estado educado; anuncia ação inelegível e falha de análise como erro assertivo e sanitizado. Conserva o foco em atualizações de fundo, anuncia a contagem final uma vez e não uma vez por linha. Em erro de formulário, move o foco para o resumo ou primeiro campo inválido e associa o erro ao controlo. |

## State Patterns

### Readiness e lifecycle

| Estado/resultados | Significado visível | Ações e tratamento |
|---|---|---|
| `PASS` | Checks aplicáveis permitem a progressão declarada. | Permitir a progressão; não exigir Override. |
| `CONCERNS` | Há condição de risco; a progressão só ocorre se a política autorizar e o risco residual for registrado. | Mostrar política aplicável e, quando exigido, caminho de decisão/aprovação. |
| `FAIL` | A progressão é impedida. | Só expor fluxo de Override se o check específico for declarado overrideable e a transição/ aprovação forem elegíveis. |
| `BLOCKED` | Há blocker vigente; não há bypass genérico. | Negar progressão. Mostrar remoção do blocker ou uma Exceção Formal pré-declarada e aplicável. |
| `AWAITING_USER` | Falta Input ou Decision humana resolvível, ou a Execution requer intervenção humana. | Identificar pendência e próxima ação; após receber o necessário, voltar a `READINESS` e reavaliar checks afetados. |
| `ACTIVE` | Trabalho autorizado está em curso. | Preservar estado da Execution; término técnico não significa `COMPLETED`. |
| `READY_TO_CLOSE` | Outputs candidatos disponíveis; Exit Contract em avaliação. | Exibir `closure-checklist`; critério obrigatório não satisfeito retorna a `ACTIVE`. |
| `COMPLETED` | Exit Contract e Gate de fechamento aprovados; Baseline criada. | Disponibilizar Baseline e opção de reabertura autorizada; não editar o histórico. |
| `REOPENED` | Nova revisão é criada a partir de trabalho concluído. | Reavaliar Inputs, Evidence e impacto em `READINESS`. |
| `STALE` | A relação material exige revalidação e, quando requisito da Stage, muda o estado local. | Não autorizar progressão automática; exigir nova Stage Revision/Readiness e revalidação de Gates/aprovações invalidados. |

### Estados de superfície

| Estado | Superfície | Tratamento |
|---|---|---|
| Carregamento inicial | Readiness, detalhe, Ledger | Esqueleto do shadcn/ui que preserva a hierarquia esperada; não mostrar um resultado de governação até receber fatos válidos. |
| Projeto sem Stage acessível | Contexto de Project | Explicar que não há Stage Revision disponível no escopo autorizado, sem revelar recursos de outro Tenant/Project. |
| Evidence indisponível ou sem permissão | Readiness, Gate, auditoria | Manter a referência e a razão de indisponibilidade; não inferir conteúdo nem expor segredo. |
| Evidence disponível | Readiness, Gate, auditoria | Mostrar metadados e referência governada. Mostrar preview somente para conteúdo explicitamente seguro/redigido; não oferecer download ou exportação. |
| Dados atualizados | Detalhe da Stage Revision | Informar que uma nova avaliação/decisão alterou os fatos exibidos e oferecer retorno ao estado atual; não sobrescrever silenciosamente uma ação em curso. |
| Ação fora da superfície permitida | Mobile | Exibir a ação e sua elegibilidade como consulta, indicando que a conclusão exige desktop/laptop; não ocultar a guarda de governança. |
| Consulta de auditoria sem resultados | Ledger ou Traceability | Informar que a consulta não retornou resultados no escopo autorizado; manter a consulta visível para ajuste e não revelar recursos fora de Tenant/Project. |
| Análise de impacto requerida, ainda não iniciada | Impacto downstream após a opção manual | Exibir pendência identificando a mudança e relações conhecidas. Até receber uma disposição por relação, a autorização ou o fechamento dependente daquelas relações permanece inelegível; a opção manual adia a execução, não a torna opcional. |
| Análise de impacto em curso | Impacto downstream | Exibir progresso enquanto a análise é executada; não apresentar o conjunto como classificação final até a análise terminar. |
| Análise de impacto falhou ou não terminou | Impacto downstream | Exibir erro sanitizado e não apresentar o conjunto incompleto como classificação final, nem relações não analisadas como `unchanged`. A operadora pode iniciar manualmente uma retentativa; as mesmas guardas de pendência continuam a valer. |
| Capability/homologação a carregar | Capability e homologação | Usar esqueleto sem afirmar elegibilidade até receber fatos válidos. |
| Capability/homologação sem Contract Test ou Evidence | Capability e homologação | Mostrar lacuna de elegibilidade e próxima ação, sem fallback silencioso. |
| Capability/homologação indisponível | Capability e homologação | Mostrar falha sanitizada e preservar a combinação/consulta pedida; não inventar suporte ou mudar de provider/modelo/executor. |

### Override e Exceção Formal

| Condição | Tratamento de experiência |
|---|---|
| Override pendente de aprovação | Mostrar a transição solicitada, aprovação exigida e estado; não a fazer parecer já autorizada. |
| Override aceito | Manter lado a lado resultado original e decisão, com escopo, validade e risco residual. Apenas a transição autorizada fica elegível. |
| Tentativa de Override sobre `BLOCKED` | Rejeitar e registrar; explicar que uma Exceção Formal distinta só é possível se já estiver declarada e aplicável. |
| Exceção Formal ativa | Manter `BLOCKED` visível, limitar as ações às enumeradas e tornar prazo/condição de encerramento inspeccionáveis. |
| Exceção expirada ou encerrada | Voltar a limitar ações ao estado governado; não tratar a exceção como solução permanente. |

### Impacto downstream

| Disposição | Tratamento de experiência |
|---|---|
| `unchanged` | Mostrar que nenhuma revalidação é exigida e conservar a análise que sustentou a disposição. |
| `REQUIRES_REVIEW` | Mostrar que a revisão é necessária antes de nova autorização ou fechamento dependente; não mudar automaticamente o estado local. |
| `STALE` | Mostrar a invalidação e a próxima revalidação; se a relação for requisito da Stage, reflectir o estado local `STALE`. |

### Consulta de auditoria e rastreabilidade

| Regra | Tratamento de experiência |
|---|---|
| Pesquisa e filtro | Ledger aceita ID/texto e filtros por período, tipo de evento, entidade, Stage Revision/Baseline, ator/origem e resultado. A URL partilhável preserva a consulta. |
| Ordenação e volume | Ledger ordena por tempo e usa paginação por cursor; não usa scroll infinito. |
| Relações | Traceability apresenta relações em lista/árvore expansível, com breadcrumbs e acesso “abrir no Ledger”; não apresenta grafo no MVP. |
| Comparação | A operadora pode comparar lado a lado duas revisões ou duas Baselines. |
| Saída de dados | O MVP não oferece exportação de Ledger, Traceability ou Evidence. |

## Interaction Primitives

O console usa os comportamentos acessíveis do shadcn/ui. Toda ação disponível no desktop deve ser alcançável por teclado com ordem de foco previsível; `Enter`/`Space` acionam controlos nativos e `Escape` fecha apenas a camada temporária superior. Atalhos globais, gestos e confirmações adicionais não foram definidos. Todo alvo independente de ponteiro mede pelo menos 24×24 CSS px, salvo exceção WCAG aplicável; controlos apenas com ícone têm nome acessível. Navegação, expansão e comparação não exigem drag: botões e links oferecem a alternativa de ponteiro único e teclado.

`Dialog`, `Popover`, `Sheet` e `Skeleton` herdam shadcn/ui sem delta visual. Os deltas comportamentais são: um diálogo/sheet modal move o foco para o título, o primeiro controlo acionável ou o primeiro erro; mantém o foco enquanto aberto, expõe controlo de fechar e devolve foco ao invocador; um popover não modal não aprisiona foco e pode ser dispensado; nenhum cabeçalho, barra fixa ou camada temporária pode ocultar completamente o alvo focado. Árvores expansíveis, comparação e camadas temporárias sempre têm saída por teclado.

Independentemente do mecanismo escolhido, as operações abaixo devem obedecer às guardas do PRD:

- iniciar/adotar uma Stage Revision registra Inputs e Provenance antes da avaliação;
- solicitar ou confirmar Override só é possível para a condição e transição explicitamente autorizadas e, no MVP, para a pessoa operadora à qual a política concede essa autoridade;
- tentar Override de `BLOCKED` é uma rejeição auditável, não um atalho;
- confirmar Exceção Formal limita-se à transição e ações enumeradas, até expirar ou encerrar, e só ocorre quando a política concede autoridade à pessoa operadora;
- autorizar Execution só ocorre com contexto, Capability, permissões e limites escopados;
- fechar só ocorre depois de Exit Contract e Gate aprovados;
- ao reabrir ou no ponto aplicável de mudança, a operadora escolhe iniciar a análise downstream automática ou manualmente; a nova revisão preserva a Baseline e dispara reavaliação/impacto conforme a escolha. A escolha manual cria a pendência de análise e mantém inelegível qualquer autorização ou fechamento que dependa de uma relação ainda sem disposição;
- qualquer caminho de auditoria navega por dados governados, não por logs brutos do runner.

## Accessibility Floor

A interface tem como alvo **WCAG 2.2 AA**. Exige operação por teclado, semântica e nomes acessíveis para leitor de ecrã, foco visível e conteúdo de estado que não dependa de cor. Deve ser entregue em pt-PT, pt-BR, inglês e espanhol e preservar a informação governada ao alternar entre tema claro e escuro. Datas, horas e números seguem o formato regional do idioma selecionado; conteúdo sem tradução exibe o original com aviso. A interface declara `lang` para o idioma selecionado, e cada passagem original não traduzida declara o seu idioma de origem conhecido; se for desconhecido, isto é exposto sem adivinhar. A preferência de sistema para reduzir movimento remove animações não essenciais. Não há uma suite manual adicional de aceite de teclado/leitor de ecrã além da verificação de conformidade WCAG 2.2 AA.

Os limites já definidos pelo produto são:

- a interface não pode expor secrets que NFR-6 proíbe no browser, Ledger, stdout/stderr persistido, prompt archive, Artifact ou Evidence;
- a explicação de Readiness, Gate, Override e impacto não pode depender exclusivamente de score opaco (NFR-10); estados, regra, Input/Evidence e razão precisam existir como informação consumível;
- tentativas cross-Tenant/cross-Project devem falhar sem revelar a existência do recurso (FR-1, NFR-4).

Antes de renderizar Evidence, a interface aplica um modelo de visualização autorizado ao Tenant/Project: remove ou redige valores portadores de segredo e usa rótulos seguros, não paths ou URLs crus. A mesma regra governa texto visível, `href`, título, nome acessível, tooltip, toast, log de cliente, URL copiada e query string. Evidence restrita ou indisponível exibe e anuncia somente estado genérico sanitizado, sem confirmar existência fora do escopo; preview passa pela mesma classificação/redação.

Em largura estreita ou zoom equivalente a 320 CSS px, Ledger e tabelas tornam-se listas semânticas de campos rotulados com detalhes expansíveis; estado, razão, próxima ação, identidade de revisão e disponibilidade de Evidence precedem campos secundários. Comparação lado a lado só usa região de rolagem horizontal rotulada quando a simultaneidade for essencial e oferece rota para detalhe não tabular. Depois de filtro, paginação, expansão ou retorno do Ledger, o foco permanece no controlo invocador para atualização no local; uma pesquisa submetida pode mover foco para o cabeçalho de resultados, e o `governance-status-message` anuncia a contagem.

O alvo de conformidade e os comportamentos de localização são decisões de UX fechadas; esta spine não cria metas adicionais de contraste, alvos ou testes além da WCAG 2.2 AA.

## Key Flows

### UJ-1 — Lia governa uma Stage do início à conclusão

1. Lia entra no Project autorizado e abre ou adota uma Stage Revision com Stage Profile, Inputs e Provenance.
2. A superfície de Readiness mostra o resultado único, os checks aplicáveis, gaps/warnings/blockers, Evidence e a ação permitida.
3. Se a progressão for elegível, Lia autoriza uma Execution compatível com a Capability Matrix e os limites escopados.
4. O Kernel recolhe Artifact, Evidence, Findings e eventos; resultados sem correlação válida são rejeitados ou ficam em quarentena para reconciliação.
5. Quando há outputs candidatos, Lia entra em `READY_TO_CLOSE` e inspeciona o Exit Contract e Gate de fechamento.
6. **Clímax:** depois que os critérios obrigatórios estão satisfeitos e o Gate é aprovado, o Kernel cria a Baseline `COMPLETED`; Lia vê o que foi produzido, por qual Execution e o risco residual vigente.

Falha: falta de Input/Decision humana leva a `AWAITING_USER` com pendência e próxima ação; blocker leva a `BLOCKED`, sem progressão.

### UJ-2 — Lia trata uma exceção sem esconder a condição original

1. Lia recebe `CONCERNS` ou `FAIL` no resumo de Readiness.
2. Ela abre o check e vê resultado original, regra, Evidence e se a política declara a condição e transição como overrideable.
3. Quando elegível, solicita Override com motivo e risco residual; se a política conceder autoridade à operadora, ela confirma a aprovação no mesmo console.
4. A decisão aceita preserva check, resultado original, política, decisor, escopo e validade.
5. **Clímax:** somente a transição explicitamente autorizada torna-se elegível; o `CONCERNS` ou `FAIL` continua consultável no mesmo contexto.

Falha: `FAIL` não declarado como overrideable, check de classe proibida ou aprovação ausente rejeita a solicitação e explica a guarda. Uma tentativa sobre `BLOCKED` é rejeitada como Override.

### UJ-2b — Lia trata `BLOCKED` por resolução ou Exceção Formal

1. Lia abre uma Stage Revision em `BLOCKED` e lê o código/razão do blocker, a Evidence e as ações impedidas.
2. A interface não oferece “Override”; mostra a remoção do blocker como caminho normal.
3. Se existir Exceção Formal pré-declarada para aquele blocker, Stage Profile/versão e transição, Lia pode inspecionar sua ativação, duração, autoridade, Evidence substituta, controle compensatório e ações permitidas.
4. A exceção só avança após a confirmação permitida pela política e continua identificada como exceção, não como Readiness `PASS`.
5. **Clímax:** a transição enumerada é autorizada de modo limitado, com `BLOCKED` e risco residual ainda consultáveis; ou o blocker removido devolve a revisão a `READINESS` para reavaliação.

Falha: exceção inexistente, expirada, fora do escopo ou relativa a área proibida não abre transição; a Stage permanece bloqueada.

### UJ-3 — Lia reabre trabalho concluído e vê o impacto

1. Lia parte de uma Baseline `COMPLETED` e solicita alteração de Input ou Decision upstream.
2. A interface conserva a Baseline anterior legível e cria uma nova Stage Revision ligada por `supersedes`, com motivo, ator, tempo e Inputs herdados/alterados.
3. A nova revisão volta a `READINESS`; Gates e aprovações cuja dependência mudou não são presumidos válidos.
4. No ponto aplicável de mudança/reabertura, Lia escolhe iniciar a análise downstream automática ou manualmente. A escolha manual cria uma pendência visível e mantém inelegíveis as ações dependentes até haver disposição; a automática inicia logo a análise. A interface mostra progresso enquanto o Kernel a executa.
5. Para cada dependência downstream, a interface mostra revisões comparadas, elementos observados, regra aplicada, Evidence, motivo e próxima ação.
6. O Kernel classifica cada relação, na ordem do PRD, como `STALE`, `REQUIRES_REVIEW` ou `unchanged`; a interface agrupa primeiro `STALE`, depois `REQUIRES_REVIEW` e por fim `unchanged`.
7. **Clímax:** Lia vê uma disposição explícita para cada relação sem que Artifact downstream seja apagado ou modificado automaticamente; cada `STALE` ou `REQUIRES_REVIEW` abre uma pendência navegável para a revisão dependente.

Falha: ausência de declaração de relação ou incerteza de materialidade resulta em `REQUIRES_REVIEW`, nunca em `unchanged`. Se a análise falhar ou não terminar, Lia vê um erro sanitizado, nenhuma classificação incompleta é apresentada como final e ela pode iniciar uma retentativa manual.

### UJ-4 — Rui reconstrói por que uma progressão foi aceita

1. Rui parte de uma Baseline, Decision, Requirement, Artifact, Evidence, Gate ou Finding dentro do Project autorizado.
2. O explorador recupera Traceability Links e eventos correlacionados do Execution/Audit Ledger.
3. Ele chega aos Inputs e versões, configuração/política, autorização de Execution, Gate, Override/exceção quando houver, Artifact, Evidence, Finding, aprovação e risco residual.
4. Correções aparecem como novo evento ou versão; a narrativa original não é reescrita.
5. **Clímax:** Rui explica a progressão aceita somente com fatos governados, sem workspace do runner ou logs brutos.

Falha: um identificador fora do Tenant/Project autenticado é negado e não revela a existência do recurso.

### UJ-5 — Marta homologa uma combinação BMAD/provider

1. Marta abre a combinação-alvo de BMAD/provider/executor no escopo autorizado.
2. Ela inspeciona as Capability observadas, limitações, versões e Evidence de Contract Test.
3. Ela executa ou consulta o Contract Test para os três Stage Profile e regista a decisão de homologação.
4. Uma Capability obrigatória ausente ou incompatível é mostrada como lacuna de elegibilidade da ação correspondente.
5. **Clímax:** a combinação homologada torna-se identificável como elegível para a ação que suporta, sem alterar a regra de Workflow.

Falha: Capability obrigatória ausente ou combinação não homologada impede autorização e explica a lacuna; o console não apresenta troca silenciosa de provider, modelo ou executor.

