# Workflow and Gates

## Workflow declarativo

Cada Stage referencia versão imutável de Workflow com Inputs, Artifact esperado, executor requerido, políticas de provider/modelo, guardrails, `review_policy`, `security_policy`, `approval_policy`, Gates, retry, timeout e transições. Declaração obrigatória ou Capability ausente rejeita Execution; um executor/provider homologado compatível pode ser trocado sem mudar semântica de produto.

## Execução autorizada e reconciliação

Antes de enviar trabalho ao Execution Plane, o Control Plane autoriza identidade, ação, contexto, Capability, permissões e limites. Só transmite contexto/capacidades necessários; autorização expirada, ausente ou incompatível é rejeitada e pode ser revogada. Ações elegíveis resultam de Stage Revision, Workflow, Interaction Mode, Autonomy Policy, permissões, Capability e Gate.

O Kernel recolhe eventos, Artifact, Evidence, Finding e resultado. Resultado sem correlação válida é rejeitado ou posto em quarentena. Divergência material entre BMAD e estado projetado bloqueia transição automática até decisão registada. Falha, timeout ou perda do runner não apaga eventos aceites; término técnico não marca `COMPLETED`.

## Perfil de fluxo BMAD obrigatório

Quando um Project adota o perfil, cada recomendação, autorização, transição e fechamento é avaliado contra a revisão vigente:

| Passagem | Artefacto validado | Falha |
| --- | --- | --- |
| Entrar em UX | PRD aprovado | `FAIL`/`BLOCKED`; só remediação de PRD. |
| Entrar em Architecture | UX validada | `FAIL`/`BLOCKED`; só remediação de UX. |
| Entrar em Project Context | Architecture aprovada | `FAIL`/`BLOCKED`; só remediação de Architecture. |
| `PASS` em Coding/Story | Project Context validado e cadeia PRD→UX→Architecture rastreável | `FAIL`/`BLOCKED`; não recomenda nem autoriza Coding/Story. |

Validação verifica identidade, revisão, autoridade, aprovação, Provenance e Traceability Link; existência de ficheiro/path ou alegação de agente não basta. Mudança material reavalia dependentes antes de nova autorização. Exceção é específica da passagem, pré-declarada, aprovada e auditada; Override genérico não dispensa artefacto obrigatório. UX, Architecture e Project Context podem ser BMAD-native, SB-native ou external/imported; o Kernel não precisa ser seu executor de autoria.

## Operação

`Guided`/`Advanced` determina detalhe apresentado ao operador. `Conservative`/`Supervised`/`Autonomous` determina ação autónoma permitida. As dimensões persistem por Project ou Stage Revision, não se inferem da persona e têm default `Guided` + `Supervised`.

`IMPORT_ADOPTION` regista identidade, origem, ownership, Provenance, versão e relação com Tenant, Project e Stage Revision; verifica completude/consistência, regista gaps e só então executa Readiness. Material sem origem ou ownership suficientes não é confiável por omissão.
