# Contrato de integração — `kick-spec` e `create-story`

Este é o contrato que os workflows chamadores devem implementar. Ele não altera workflows por si só.

## Ponto de chamada

1. Gere o artefato candidato (spec ou story) em estado não aprovado.
2. Execute `bmad-review-security` com o caminho do artefato e os materiais de suporte necessários.
3. Persista ou anexe o relatório e a decisão de revisão à versão exata do artefato.
4. Volte o artefato para correção se houver bloqueio; execute nova revisão após a correção, preservando a rastreabilidade da anterior.
5. Somente permita a transição indicada abaixo quando a decisão e as condições forem satisfeitas.

## `kick-spec`

- Chame após a geração da spec e antes de “aprovada”, “pronta” ou estado equivalente.
- Bloqueie a transição se faltar seção STRIDE, se houver achado Alto Risco aberto ou se uma verificação essencial estiver indisponível sem aceitação formal de risco.
- Exija a identificação de dono de risco, mitigações e testes para ameaças aplicáveis.

## `create-story`

- Chame para **cada** story recém-criada, antes de “ready for development” ou equivalente.
- Inclua no corpo/metadados da story: referência ao relatório, data/versão analisada, resumo de riscos, controles/testes exigidos e estado de cada achado.
- Bloqueie a transição se existir Alto Risco aberto, STRIDE ausente quando a story cria/altera fluxo de risco, ou não houver revisor de segurança independente demonstrável.

## Regras de execução

- O agente/autoria de desenvolvimento não aprova sua própria revisão de segurança.
- A aceitação de risco é explícita, datada, limitada e atribuída; não apaga o achado nem equivale a correção.
- Mudanças arquiteturais ou em `implementation_plan.md` requerem aprovação humana antes de serem aplicadas.
- Se o workflow não conseguir executar a revisão, ele falha fechado: marque o artefato como bloqueado, com razão e próxima ação, sem alegar que o gate foi cumprido.
