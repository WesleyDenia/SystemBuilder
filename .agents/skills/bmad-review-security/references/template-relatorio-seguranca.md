# Template — Relatório de Revisão Adversarial de Segurança

## Cabeçalho

- Data e revisor independente:
- Escopo e versão/revisão:
- Tipo: planejamento, código, automação ou misto
- Materiais não acessíveis / limitações:

## Evidência de verificações

| Verificação | Comando/ferramenta e versão | Alvo | Resultado | Limitação |
|---|---|---|---|---|
| | | | | |

## 🔴 Alto Risco

### [ID] Título conciso

- **Item / componente afetado:** arquivo:linha, endpoint, fluxo ou seção da spec.
- **Risco detectado:** descrição técnica e condição de exploração.
- **Impacto para o projeto:** consequência técnica e de negócio.
- **Solução recomendada:** alteração, controle ou teste específico.
- **Evidência:** trecho sanitizado, comando, resultado ou referência verificável.
- **Estado:** aberto, mitigado e revalidado, aceite formal, ou não verificável.

Registre `Nenhum achado no escopo.` quando aplicável.

## 🟡 Médio Risco

Repita os campos obrigatórios por achado: item/componente afetado, risco detectado, impacto, solução recomendada, evidência e estado.

## 🟢 Baixo Risco

Repita os campos obrigatórios por achado: item/componente afetado, risco detectado, impacto, solução recomendada, evidência e estado.

## Cobertura STRIDE e governança

| Área | Resultado | Evidência / pendência |
|---|---|---|
| Spoofing | | |
| Tampering | | |
| Repudiation | | |
| Information Disclosure | | |
| Denial of Service | | |
| Elevation of Privilege | | |
| Segregação Dev × Security | | |
| Aprovação humana de arquitetura/plano | | |
| Política de artefatos/IDE e sandbox | | |

## Decisão

Escolha uma: **BLOQUEADO**, **APROVÁVEL COM RESSALVAS**, **SEM BLOQUEIOS ENCONTRADOS NO ESCOPO**.

Justifique a decisão, liste os bloqueios e as ações/aceitações necessárias. Não use a palavra “seguro” como garantia absoluta.
