# Reconciliação — Stage Contract v0.1

Fonte reconciliada: `../spec-systembuilder-control-plane-kernel/stage-contract.md`.

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

## Cobertura forte

- FR-2 preserva as sete seções do contrato; FR-3 preserva os nove estados locais e separa estado da Stage Revision, Readiness Result, Execution e Finding.
- FR-4 e FR-6 refletem corretamente `PASS`, `CONCERNS`, `FAIL` e `BLOCKED`, incluindo Override estreito para `FAIL`, risco residual e ausência de bypass genérico de `BLOCKED`.
- FR-13 a FR-15 cobrem Exit Contract, Baseline, reabertura como nova revisão, `supersedes` e disposição downstream não destrutiva.
- FR-18 a FR-20 representam os três perfis exigidos; o Coding/Story mantém Gate antes/depois de desenvolvimento e rejeita conclusão baseada apenas em geração de código.

## Gaps

1. **O conteúdo normativo das sete seções está subespecificado.** FR-2 nomeia as seções, mas seus aceites não preservam itens mandatórios da tabela da fonte, como owner e BMAD capability target em Identity; required/optional inputs e imported refs em Entry; autonomy/permissions/human decisions em Execution; e impact/staleness/reopening rules em Dependencies.
2. **O conjunto inicial de transições não é verificável.** FR-3 aceita quaisquer transições declaradas, mas não exige o caminho normal nem os interruption/reopening paths da fonte (`READINESS -> AWAITING_USER|BLOCKED`, `ACTIVE -> AWAITING_USER|BLOCKED`, `READY_TO_CLOSE -> ACTIVE`, `COMPLETED -> REOPENED -> ACTIVE`).
3. **Ideation/Brainstorm enfraquece outputs mandatórios.** FR-18 diz que a saída “candidata inclui” brainstorm e conteúdo de product brief. Na fonte, ambos são Exit outputs e o product brief deve conter problema/oportunidade, proposta de valor, hipóteses, features iniciais e contexto PRD-ready. Também faltam aceites para `CONCERNS` por assumptions fracas e `FAIL` por problema/usuário/outcome ambíguos.
4. **Os perfis PRD e Coding/Story omitem checks específicos.** FR-19 não explicita aprovação do PRD nem todos os outputs (business rules, dependencies e product acceptance criteria). FR-20 não exige no Readiness branch/base commit e execution policy autorizados e não nomeia migration, contract e dependency checks no Exit quando aplicáveis.

## Conflitos ou tensões

- **Saída “candidata” versus Exit obrigatório:** FR-18 é mais fraco que a fonte e permitiria concluir Ideation sem os outputs que caracterizam o perfil.
- **Finding bloqueante formalmente tratado:** FR-20 admite fechar com Findings bloqueantes “resolvidos ou formalmente tratados”, enquanto a seção Coding/Story da fonte diz que code review e security review não podem ter blocking findings. O companion `assurance-core.md` admite tratamento formal sob governança; portanto, o PRD precisa definir que um tratamento formal muda a classificação/efeito do Finding segundo política específica, e não deixa um blocker ativo no fechamento.
- A fonte usa `stage_version` na identidade e `stage_revision` no Assurance Core. O glossário do PRD distingue versão do contrato de ocorrência da Stage, mas FR-2 deve tornar esse mapeamento explícito para evitar implementações incompatíveis.

## Correções recomendadas

1. Incorporar em FR-2 uma tabela ou referência normativa com o conteúdo obrigatório de cada seção e explicitar `stage_version` versus `stage_revision`.
2. Acrescentar em FR-3 os caminhos mínimos normativos do v0.1; extensões continuam declarativas e versionadas.
3. Tornar obrigatórios em FR-18 o brainstorm consolidado e o product brief com os cinco elementos da fonte, além de checks de Readiness para os quatro resultados.
4. Completar FR-19 e FR-20 com os inputs/outputs/checks omitidos e esclarecer que nenhuma Stage fecha enquanto um Finding continuar efetivamente bloqueante.
