---
name: SystemBuilder Control Plane Kernel
description: Visual contract for the responsive internal web console that governs Stage progression.
status: final
created: 2026-10-14
updated: 2026-10-15
sources:
  - ../prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md
design-system: shadcn/ui
theme-source: ../../../docs/theme.css
colors:
  background: '#FFFFFF'
  background-dark: '#090B0C'
  foreground: '#090B0C'
  foreground-dark: '#F9FBFB'
  card: '#FFFFFF'
  card-dark: '#161B1D'
  card-foreground: '#090B0C'
  card-foreground-dark: '#F9FBFB'
  popover: '#FFFFFF'
  popover-dark: '#161B1D'
  popover-foreground: '#090B0C'
  popover-foreground-dark: '#F9FBFB'
  primary: '#BB4D00'
  primary-dark: '#973C00'
  primary-foreground: '#FFFBEB'
  primary-foreground-dark: '#FFFBEB'
  secondary: '#F4F4F5'
  secondary-dark: '#27272A'
  secondary-foreground: '#18181B'
  secondary-foreground-dark: '#FAFAFA'
  muted: '#F1F3F3'
  muted-dark: '#22292B'
  muted-foreground: '#67787C'
  muted-foreground-dark: '#9CA8AB'
  accent: '#F1F3F3'
  accent-dark: '#22292B'
  accent-foreground: '#161B1D'
  accent-foreground-dark: '#F9FBFB'
  destructive: '#E7000B'
  destructive-dark: '#FF6467'
  border: '#E3E7E8'
  border-dark: '#FFFFFF1A'
  input: '#E3E7E8'
  input-dark: '#FFFFFF26'
  ring: '#9CA8AB'
  ring-dark: '#67787C'
  sidebar: '#F9FBFB'
  sidebar-dark: '#161B1D'
  sidebar-foreground: '#090B0C'
  sidebar-foreground-dark: '#F9FBFB'
  sidebar-primary: '#E17100'
  sidebar-primary-dark: '#FE9A00'
  sidebar-primary-foreground: '#FFFBEB'
  sidebar-primary-foreground-dark: '#461901'
  sidebar-accent: '#F1F3F3'
  sidebar-accent-dark: '#22292B'
  sidebar-accent-foreground: '#161B1D'
  sidebar-accent-foreground-dark: '#F9FBFB'
  sidebar-border: '#E3E7E8'
  sidebar-border-dark: '#FFFFFF1A'
  sidebar-ring: '#9CA8AB'
  sidebar-ring-dark: '#67787C'
typography:
  interface:
    note: 'Inherited from shadcn/ui; docs/theme.css declares no typography override.'
rounded:
  DEFAULT: 0.625rem
spacing:
  system:
    note: 'Inherited unchanged from shadcn/ui; docs/theme.css declares no spacing override.'
components:
  readiness-summary:
    surface: '{colors.card}'
    foreground: '{colors.card-foreground}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  check-result-row:
    divider: '{colors.border}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  gate-record:
    surface: '{colors.card}'
    divider: '{colors.border}'
  override-record:
    surface: '{colors.card}'
    divider: '{colors.border}'
  blocked-resolution:
    surface: '{colors.card}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  formal-exception-record:
    surface: '{colors.card}'
    divider: '{colors.border}'
  closure-checklist:
    divider: '{colors.border}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  revision-lineage:
    divider: '{colors.border}'
  downstream-impact-relation:
    divider: '{colors.border}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  audit-event-row:
    divider: '{colors.border}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  traceability-explorer:
    divider: '{colors.border}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
  governance-status-message:
    foreground: '{colors.foreground}'
    error-foreground: '{colors.destructive}'
    focus-ring: '{colors.primary}'
    focus-ring-dark: '{colors.primary-foreground-dark}'
---

## Brand & Style

O Control Plane Kernel é um console web interno para trabalho individual governado. A postura é a de uma ferramenta de operação: sóbria, precisa e orientada à inspeção. A pessoa operadora deve localizar o Readiness atual, compreender a razão e executar a próxima ação elegível sem interpretar logs brutos ou depender de um score opaco.

O console herda o sistema visual do **shadcn/ui** e o tema aprovado em [`docs/theme.css`](../../../docs/theme.css). Este documento não introduz uma camada de marca, tipografia ou paleta paralela: os tokens declarados acima são a representação hexadecimal, sob nomes `*-dark`, dos valores de `:root` e `.dark` daquele ficheiro. O ficheiro de tema é a autoridade de implementação para esses tokens; estas spines prevalecem sobre futuros mockups ou imports em caso de conflito.

Resultados, riscos e ações elegíveis permanecem distinguíveis por texto: resultado original, política/regra aplicada, Evidence relacionada, risco residual, ator/origem e tempo. A explicabilidade vem de dados e linguagem, não de cor isolada.

## Colors

Os tokens `colors` são a representação hexadecimal dos valores existentes em `docs/theme.css`, aplicados pelo tema claro ou escuro correspondente. `{colors.primary}` e `{colors.primary-dark}` servem às ações primárias elegíveis; `{colors.destructive}` e `{colors.destructive-dark}` ficam reservados ao tratamento destrutivo do sistema. Não há uma atribuição visual adicional aprovada para `PASS`, `CONCERNS`, `FAIL`, `BLOCKED`, `unchanged`, `REQUIRES_REVIEW` ou `STALE`.

Cada condição de governança exige rótulo e razão textual. Cor, ícone ou posição nunca são a única forma de identificar uma condição. As combinações efetivamente usadas devem cumprir o alvo WCAG 2.2 AA nos dois temas; não foi definido limiar adicional específico do produto.

O tema é preservado, mas nem toda combinação de tokens é permitida neste console. Em superfícies claras neutras, foco usa `{colors.primary}`; em superfícies escuras, `{colors.primary-foreground-dark}`. Texto secundário em tamanho normal usa um foreground aprovado que cumpra AA no fundo em uso — `{colors.muted-foreground}` não pode ser usado sobre `{colors.muted}` no tema claro. Rótulos em `{colors.sidebar-primary}` usam `{colors.sidebar-foreground}`, não `{colors.sidebar-primary-foreground}`. Input e select não podem depender apenas de `{colors.border}` ou `{colors.input}` no tema claro: usam uma fronteira/indicador persistente de pelo menos 3:1 com `{colors.primary}` ou outro token aprovado para a superfície. Estados disabled seguem as exceções WCAG aplicáveis e nunca representam a única via de uma ação de governança.

## Typography

Tipografia, escala e pesos herdam o shadcn/ui conforme `{typography.interface}`. Identificadores, versões, timestamps, regras e referências de Evidence usam a hierarquia normal do sistema e não podem ser truncados sem uma forma acessível de consultar o valor completo. Não introduzir fonte de display ou escala proprietária.

## Layout & Spacing

Desktop e laptop são a superfície completa. A composição privilegia um workspace de Readiness: contexto da Stage, resultado e próxima ação aparecem antes de detalhes expansíveis de checks, Gate e Evidence. Listas longas usam tabela ou lista do shadcn/ui conforme a informação precise de comparação ou leitura linear.

Em telemóvel, o console é para consulta: estado, razão, próxima ação e rastreabilidade continuam legíveis, mas ações críticas de progressão, aprovação, Override, Exceção Formal e fechamento não são oferecidas. Breakpoints, grid e escala de espaçamento herdam o sistema conforme `{spacing.system}`; não há grid proprietário definido.

## Elevation & Depth

Herdado do shadcn/ui. Elevação é reservada para superfícies temporárias, como `Dialog`, `Popover` ou `Sheet`; não deve transformar cada registro de governança num cartão elevado. Hierarquia vem de agrupamento, tipografia, separadores e ordem de leitura.

## Shapes

Raios, bordas e estados de foco herdam o shadcn/ui e o raio base `{rounded.DEFAULT}` de `docs/theme.css`. Não criar chips decorativos, badges semânticos sem texto ou formas que façam `BLOCKED` parecer uma ação apelativa.

## Components

Os nomes abaixo são unidades semânticas compartilhadas com `EXPERIENCE.md.Component Patterns`. Todos usam primitivas shadcn/ui e os tokens de componente declarados no frontmatter; os estados semânticos preservam texto e razão, sem mapa de cor proprietário.

| Componente | Propósito visual | Regra visual |
|---|---|---|
| `readiness-summary` | Região de resumo do estado atual. | Usa `{components.readiness-summary.surface}`; resultado, razão e ação são texto principal; foco usa os tokens explícitos claro/escuro do componente. |
| `check-result-row` | Linha expansível de check. | Usa `{components.check-result-row.divider}`; apresenta resultado, regra, Evidence e explicação; foco usa os tokens explícitos claro/escuro do componente. |
| `gate-record` | Detalhe comparável de Gate. | Usa `{components.gate-record.surface}` e `{components.gate-record.divider}`; ordena critérios e metadados para leitura. |
| `override-record` | Registo de decisão de Override. | Usa `{components.override-record.surface}`; agrupa resultado original e decisão sem esconder a condição de origem. |
| `blocked-resolution` | Região de blocker e caminhos elegíveis. | Usa `{components.blocked-resolution.surface}`; não apresenta Override genérico como ação. |
| `formal-exception-record` | Registo distinto de Override. | Usa `{components.formal-exception-record.surface}`; escopo, validade e controlo compensatório permanecem legíveis em texto. |
| `closure-checklist` | Lista de critérios de saída. | Usa `{components.closure-checklist.divider}`; estado, justificativa e Evidence permanecem textuais. |
| `revision-lineage` | Linhagem de Baseline e revisão. | Usa `{components.revision-lineage.divider}`; preserva Baseline e `supersedes` numa sequência legível. |
| `downstream-impact-relation` | Relação downstream e sua disposição. | Usa `{components.downstream-impact-relation.divider}`; ordem do conjunto é `STALE`, `REQUIRES_REVIEW`, `unchanged`. |
| `audit-event-row` | Evento append-oriented do Ledger. | Usa `{components.audit-event-row.divider}`; trata correlação e entidades como dados de leitura, não decoração. |
| `traceability-explorer` | Navegação em relações. | Usa `{components.traceability-explorer.divider}`; relações expansíveis usam lista/árvore, não grafo no MVP. |
| `governance-status-message` | Mensagem de alteração assíncrona ou de guarda de governança. | Usa `{components.governance-status-message.foreground}`; erro usa `{components.governance-status-message.error-foreground}` e nunca inclui dados restritos. |

## Do's and Don'ts

| Fazer | Não fazer |
|---|---|
| Preservar e identificar o resultado original ao mostrar correções, nova avaliação, Override ou exceção. | Fazer um `FAIL` ou `BLOCKED` parecer `PASS` por estilo, cópia ou remoção do contexto. |
| Usar nomes de estado e explicações textuais junto de qualquer tratamento visual. | Fazer cor, ícone ou score opaco ser a única explicação de estado ou impacto. |
| Aplicar exclusivamente os tokens aprovados em `docs/theme.css` e a herança shadcn/ui. | Criar uma camada de marca, cor, tipografia ou mapa semântico não aprovado. |
| Manter Baseline, versões e relações históricas distinguíveis. | Apresentar a reabertura como edição destrutiva da revisão concluída. |
| Mostrar Evidence como metadados e referências governadas; mostrar preview apenas quando o conteúdo for explicitamente seguro/redigido. | Expor segredo, stdout/stderr persistido ou logs brutos como substituto de Evidence governada. |
| Manter ações críticas no desktop/laptop e leitura essencial em mobile. | Permitir aprovar, fechar ou criar exceção numa viewport móvel. |
