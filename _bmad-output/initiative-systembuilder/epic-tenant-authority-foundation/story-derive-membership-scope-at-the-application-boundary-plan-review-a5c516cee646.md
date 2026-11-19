# Gate de revisão do plano — Story 1.3

- **Data:** 2026-11-19
- **Plano revisto:** `story-derive-membership-scope-at-the-application-boundary-plan.md`
- **SHA-256 do plano:** `a5c516cee646f3fb9aa323d7738639a274a37ff31c1206edf6154e87853f5c0b`
- **Sequência obrigatória:** adversarial → segurança independente
- **Independência:** revisores adversarial e de segurança independentes do autor do plano; a revisão de segurança recebeu os achados adversariais apenas como contexto.
- **Escopo:** planejamento; nenhuma implementação, infraestrutura ou teste foi alterado/executado neste gate.

## Resultado do gate

**BLOQUEADO.** O plano permanece em `draft`. Há três riscos altos abertos: serialização canónica do token, consumo atómico conjunto de intenção e `jti`, e identidade mTLS verificável do workload BFF. Qualquer correção do plano requer autorização humana e uma nova revisão completa.

## Revisão adversarial

1. **Verifier HMAC v2:** JSON/JWT com chaves duplicadas, Unicode confusável ou números ambíguos pode ter semântica divergente. **Guarda:** rejeitar JSON não canónico, duplicados, UTF-8 inválido e representações críticas alternativas antes da assinatura. **Consequência:** `aud`, `kid`, `exp` ou ação interpretados diferentemente.
2. **`kid`:** formatos não canónicos, prefixos, case-folding ou cache desatualizado podem selecionar chave errada. **Guarda:** ASCII de tamanho fixo, lookup exato em keyring autoritativo e invalidação imediata de cache. **Consequência:** chave revogada ainda valida.
3. **Emissão da intenção:** criação antes de regeneração/logout/alteração de credencial deixa sessão antiga utilizável. **Guarda:** vincular a versão da sessão/autenticação e revalidá-la atomicamente no consumo. **Consequência:** intenção de sessão revogada é consumida.
4. **Intenção e ReplayGuard:** reserva de `jti` e consumo da intenção separados permitem corrida. **Guarda:** CAS único que transiciona ambos a consumidos, com correlação imutável. **Consequência:** reutilização de intenção ou queima isolada de `jti`.
5. **Snapshot:** leitura posterior sem versão/epoch transportada permite revogação entre autorização e repo. **Guarda:** versão de membership, RBAC, sessão e Project no contexto e verificação no acesso. **Consequência:** leitura com decisão obsoleta.
6. **Contexto/ports:** adapter opcional, array, `null` ou identidade direta pode contornar factory. **Guarda:** contexto obrigatório, selado, sem métodos alternativos. **Consequência:** endpoint/job lê sem scope reautorizado.
7. **Rejeição precoce:** middleware, bindings, auditoria ou exceções podem tocar input antes da borda normalizadora. **Guarda:** adaptador mínimo antes de logging, binding, model resolution ou middleware consumidor. **Consequência:** seletor/credencial chega a logs, erros ou consultas implícitas.
8. **mTLS do BFF:** validar só cadeia/hostname não liga certificado a workload, ambiente, audiência e revogação. **Guarda:** SAN/SPIFFE exato, mapeamento autorizado, audiência/ambiente/validade/revogação por pedido. **Consequência:** outro workload certificado atua como BFF.
9. **Rota privada:** proxies, mesh, túneis ou portas alternativas podem expor endpoint. **Guarda:** Laravel autentica workload e nega por defeito em toda exposição, independente de topologia. **Consequência:** caminho interno chama endpoint sem BFF-only.
10. **Audience:** string livre gera confusão de ambiente/operação. **Guarda:** enumeração fechada por deployment e igualdade byte a byte. **Consequência:** token de outra operação/ambiente reutilizado.
11. **Correlação:** formato, unicidade e logging seguro não estão fechados. **Guarda:** identificador opaco canónico gerado pelo servidor, vinculado à intenção e nunca interpolado. **Consequência:** colisão, interferência ou injeção em telemetria.
12. **Negações:** canais além de corpo/headers/cache/tamanho/timing permanecem. **Guarda:** resposta única sem cookies, trailers ou headers condicionais; equivalência integral. **Consequência:** enumeração de estado/recurso.
13. **Teste de zero repo no deny:** apenas repo de domínio pode estar instrumentado. **Guarda:** instrumentar persistência, cache e Evidence. **Consequência:** divulgação por acesso auxiliar.
14. **Harness:** atestação baseada em ambiente/configuração pode vazar. **Guarda:** capability de teste não serializável e abortar boot fora do harness. **Consequência:** fixture permissiva em deploy.
15. **DoS do ReplayGuard:** muitos `jti` distintos podem esgotar store. **Guarda:** quotas/cardinalidade por workload, principal e sessão, com limpeza determinística. **Consequência:** bloqueio de consumos legítimos.

## Revisão de segurança independente

### Cabeçalho

- **Escopo/revisão:** plano da Story 1.3, `a5c516cee646f3fb9aa323d7738639a274a37ff31c1206edf6154e87853f5c0b`.
- **Tipo:** planejamento.
- **Limitações:** sem código, configuração, infraestrutura, testes ou evidência de execução; não é possível confirmar implementação ou propriedades operacionais.

### Evidência de verificações

| Verificação | Evidência disponível | Resultado |
| --- | --- | --- |
| Autoridade Laravel/reautorização | sessão, principal, membership, Project e RBAC em emissão/consumo | Planeado |
| Browser restrito ao BFF | browser→BFF e segredo BFF fora do browser | Planeado |
| Claims/criptografia | HMAC-SHA-256, `iss`, `kid`, UTC, TTL ≤60s, schema estrito | Planeado |
| Antirreplay | reserva/confirmação `jti`, CAS linearizável e fail-closed | Planeado |
| Negação precoce | matriz/tarefas exigem antes de Application/repo | Planeado |
| Contexto de scope | `ScopedReadContext` imutável/não serializável nos ports | Planeado |
| Segredos/Evidence | DTO sanitizado, allowlist/redação e proibições | Planeado |
| Isolamento de testes | fixture/mutex e abort fora de testes | Planeado |
| Verificação real | nenhum artefato executável acessível | Não verificável |

### 🔴 Alto Risco

#### SEC-1 — Serialização canónica e validação estrita do token

- **Item / componente afetado:** parser/schema/normalização da delegação v2.
- **Risco detectado:** chaves JSON duplicadas, Unicode, números/tempos e `aud` podem ser interpretados diferentemente entre componentes.
- **Impacto para o projeto:** integridade de claims, audience ou autorização comprometida.
- **Solução recomendada:** contrato binário/canónico: rejeitar duplicados, Unicode/tipos alternativos e campos não previstos; igualdade exata de `aud`, `iss`, `kid`, `action` e ambiente contra enums autoritativos; testes de serialização divergente.
- **Evidência:** “schema+normalization first adapter” não define canonicalização, duplicados, Unicode ou tipos.
- **Estado:** aberto — bloqueante.

#### SEC-2 — Consumo atómico da intenção e do `jti`

- **Item / componente afetado:** intenção one-use, ReplayGuard e reautorização.
- **Risco detectado:** não está explícito que ambos são consumidos terminalmente em um único CAS, nem que há versão/epoch de sessão/autenticação.
- **Impacto para o projeto:** corrida, dupla execução ou intenção revogada/trocada entre validações.
- **Solução recomendada:** uma transação/CAS linearizável que valide versão de sessão/autenticação, principal, workload, audience, ação, alvo, correlação, expiração/revogação e consuma intenção e `jti` juntos; definir estados e idempotência.
- **Evidência:** CAS é exigido para ReplayGuard, intenção é one-use/60s, mas atomicidade conjunta está ausente.
- **Estado:** aberto — bloqueante.

#### SEC-3 — Identidade mTLS verificável do BFF

- **Item / componente afetado:** autenticação de workload BFF → Laravel.
- **Risco detectado:** não há definição de identidade de certificado, mapeamento, revogação ou independência da topologia.
- **Impacto para o projeto:** serviço/proxy/workload indevidamente confiado pode consumir delegação e elevar privilégios.
- **Solução recomendada:** mTLS obrigatório com cadeia, SAN/SPIFFE exato, mapeamento workload→ambiente→audience/operações, rotação/revogação e rejeição de headers de identidade encaminhados; Laravel autentica criptograficamente sem confiar em IP/rede/`Forwarded`.
- **Evidência:** tarefas citam mTLS e proibição de forwarded, sem binding SAN/SPIFFE, revogação ou mapeamento.
- **Estado:** aberto — bloqueante.

### 🟡 Médio Risco

#### SEC-4 — Ciclo de vida do keyring HMAC

- **Item / componente afetado:** `kid`/keyring.
- **Risco detectado:** formato, fonte, cache, rotação/revogação imediata não definidos.
- **Impacto:** aceitação indevida entre ambientes ou durante rotação.
- **Solução recomendada:** `kid` ASCII/exato, keyring por ambiente ativo/revogado, TTL limitado, invalidação distribuída e teste de revogação durante token válido.
- **Evidência:** allowlist/ativo sem ciclo de vida operacional.
- **Estado:** aberto.

#### SEC-5 — Completude do contexto em todas as portas

- **Item / componente afetado:** persistência, cache, Evidence e queries indiretas.
- **Risco detectado:** não há inventário que prove todos os caminhos selados nem epoch no repositório.
- **Impacto:** leitura cross-tenant/project por adapter omitido.
- **Solução recomendada:** capability selada/obrigatória, epoch no contexto revalidada na fronteira, instrumentação de todos os adapters em negações.
- **Evidência:** Code Map cobre ports sem enumerar completude.
- **Estado:** aberto.

#### SEC-6 — Equivalência integral de negações

- **Item / componente afetado:** resposta HTTP e efeitos laterais.
- **Risco detectado:** cookies, trailers, ordem de headers, redirects e efeitos secundários não são explicitamente cobertos.
- **Impacto:** enumeração de membership, Project, RBAC, intenção ou revogação.
- **Solução recomendada:** envelope único com status, corpo, headers, cookies, trailers, cache, tamanho e sem redirects; testes por motivo de negação.
- **Evidência:** plano cobre parte dos canais.
- **Estado:** aberto.

#### SEC-7 — Capacidade/quotas do ReplayGuard

- **Item / componente afetado:** store/replay/intenções.
- **Risco detectado:** sem quotas, cardinalidade, rate limits ou capacidade.
- **Impacto:** exaustão do store/CAS e DoS.
- **Solução recomendada:** quotas/tamanho/taxa/TTL por principal, workload e ambiente, métricas sanitizadas e testes de exaustão/degradação.
- **Evidência:** há TTL/fail-closed sem política de capacidade.
- **Estado:** aberto.

### 🟢 Baixo Risco

#### SEC-8 — Inventário verificável de v1

- **Item / componente afetado:** rotas, harnesses, flags e documentação.
- **Risco detectado:** inventário/remoção não define critério de completude.
- **Impacto:** compatibilidade v1 residual.
- **Solução recomendada:** inventário versionado e teste negativo de indisponibilidade em produção.
- **Evidência:** tarefa prevê inventário/remoção.
- **Estado:** aberto, verificável na implementação.

#### SEC-9 — Telemetria sanitizada de recusa

- **Item / componente afetado:** auditoria/observabilidade.
- **Risco detectado:** não há schema de metadados mínimos para investigar abuso sem fuga.
- **Impacto:** menor investigação de replay/revogação ou risco de logs ad hoc.
- **Solução recomendada:** evento sanitizado com correlação opaca, código de política, workload pseudonimizado e sequência; proibir claims/token/segredo/alvo/Evidence.
- **Evidência:** plano proíbe material sensível, sem schema de auditoria.
- **Estado:** aberto.

### Cobertura STRIDE e governança

| Área | Resultado | Evidência / pendência |
| --- | --- | --- |
| Spoofing | Parcial | HMAC/`iss`/`kid`/audience previstos; mTLS/keyring/revogação incompletos |
| Tampering | Parcial | assinatura/schema previstos; canonicalização/duplicados/Unicode ausentes |
| Repudiation | Parcial | correlação/intenção/`jti` previstos; CAS conjunto e auditoria sanitizada ausentes |
| Information Disclosure | Parcial | DTO/Evidence/redação previstos; envelope completo de negação pendente |
| Denial of Service | Parcial | TTL/fail-closed previstos; quotas/cardinalidade/capacidade ausentes |
| Elevation of Privilege | Parcial | reautorização/snapshot fortes; contexto selado completo/epoch pendentes |
| Segregação Dev × Security | Não verificável | sem dono independente, controlo de keyring/policy ou evidência operacional |
| Aprovação humana | Não verificável | sem approver, gate/evidência ou autoridade de exceções |
| Política de artefatos/IDE e sandbox | Parcial | browser/Evidence cobertos; faltam testes, IDE, dumps, traces, cache e credenciais workload |

### Decisão

**BLOQUEADO.** Apesar da boa orientação fail-closed e defesa em profundidade, SEC-1, SEC-2 e SEC-3 são riscos altos abertos. O plano não pode avançar para aprovação ou implementação antes de os corrigir e reapresentar ao gate.
