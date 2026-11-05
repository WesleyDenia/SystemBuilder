# Template — Threat Modeling STRIDE

Use este template em PRDs, Blueprints, Specs e histórias. Preencha com evidência do sistema; não use texto genérico como substituto.

## Contexto e escopo

- Artefato / versão:
- Dono técnico e dono de risco:
- Fluxo em análise:
- Ativos a proteger (incluindo dados pessoais, segredos e decisões governadas):
- Fora de escopo e pressupostos validados:

## Fronteiras e fluxos de dados

| Origem | Entrada | Processamento | Saída / destino | Confiança e classificação |
|---|---|---|---|---|
| | | | | |

Descreva identidade, autenticação, autorização, isolamento tenant/projeto, limites de payload/taxa, retenção e erros em cada fronteira.

## Ameaças STRIDE

| Categoria | Ameaça / cenário de abuso | Componente e fronteira | Mitigação / controle | Evidência e teste | Risco residual | Dono |
|---|---|---|---|---|---|---|
| Spoofing | | | | | | |
| Tampering | | | | | | |
| Repudiation | | | | | | |
| Information Disclosure | | | | | | |
| Denial of Service | | | | | | |
| Elevation of Privilege | | | | | | |

## Critérios de saída

- [ ] Entradas e saídas, identidade, autorização e limites foram mapeados.
- [ ] Controles verificáveis e testes foram definidos para cada ameaça relevante.
- [ ] Riscos residuais têm responsável e aceitação explícita quando aplicável.
- [ ] Nenhum segredo ou dado pessoal foi incluído neste documento.
