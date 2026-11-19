# Gate de revisão do plano — Story 1.3

- **Data:** 2026-11-19
- **Plano revisto:** `story-derive-membership-scope-at-the-application-boundary-plan.md`
- **SHA-256 do plano:** `f533fcaf3a427dd8ed0ce1b3ac53df6cb523398721e1dfbb4982489feb349f11`
- **Sequência obrigatória:** adversarial → segurança independente
- **Independência:** a revisão adversarial e a revisão de segurança foram conduzidas por revisores independentes do autor do plano. A segunda recebeu os achados adversariais como contexto, mantendo avaliação própria.
- **Escopo:** planejamento da Story 1.3; nenhuma implementação foi alterada ou executada.

## Resultado do gate

**BLOQUEADO.** O plano permanece em `draft`. A revisão de segurança encontrou riscos altos abertos relativos ao perfil criptográfico da delegação v2, à atomicidade/TOCTOU do consumo de `jti` e à autenticação de serviço entre BFF e Laravel. Uma alteração do plano requer nova aprovação humana e um novo gate completo.

## Revisão adversarial

1. **Localização:** Delegação v2 / verificador HMAC
   **Condição:** algoritmo, `kid`, emissor, assinatura truncada ou rotação de chaves não são fechados.
   **Guarda:** aceitar somente algoritmo fixo, `iss` literal, `kid` allowlisted, chave ativa e comparação constante; rejeitar ausentes/desconhecidos.
   **Consequência:** token forjado, confusão de algoritmo ou chave inadequada.

2. **Localização:** Binding de `correlation_id`
   **Condição:** delegação válida é reutilizada em outra requisição, sessão, dispositivo ou conexão BFF.
   **Guarda:** correlação criada pelo servidor, ligada atomicamente a sessão, principal, ação, intenção e requisição BFF esperada.
   **Consequência:** replay autorizado fora do fluxo original.

3. **Localização:** Consumo de `jti` pré-Application
   **Condição:** o replay guard consome antes de sessão, membership, RBAC e intenção terminarem a revalidação.
   **Guarda:** reserva pendente e confirmação na mesma operação atómica da autorização; expirar reservas falhadas.
   **Consequência:** negação de serviço ao pedido legítimo.

4. **Localização:** Revalidação de autorização
   **Condição:** membership, assignment ou RBAC mudam entre verificações e a chamada ao repositório.
   **Guarda:** resolver sessão, membership, assignment, RBAC, intenção e consumo sob snapshot/lock consistente.
   **Consequência:** leitura após revogação efetiva por janela TOCTOU.

5. **Localização:** Replay guard distribuído
   **Condição:** múltiplas instâncias Laravel, reinício, partição Redis ou réplica eventual do estado de `jti`.
   **Guarda:** compare-and-set linearizável com TTL, namespace de ambiente e fail-closed em erro, timeout ou réplica não confirmada.
   **Consequência:** mesmo `jti` aceite em nós distintos.

6. **Localização:** Normalização HTTP
   **Condição:** proxy, PHP/Laravel e middleware interpretam diferentemente headers duplicados, casing, rota codificada ou múltiplos `Authorization`.
   **Guarda:** normalizar/rejeitar ambiguidade no primeiro adaptador confiável, antes da autenticação.
   **Consequência:** seletor de scope proibido escapa à deteção ou influencia camada intermédia.

7. **Localização:** Fonte de autenticação da sessão
   **Condição:** cookie, bearer, credencial BFF e headers de identidade conflitantes coexistem.
   **Guarda:** modalidade autenticada única por rota; rejeitar credenciais concorrentes ou identidade divergente.
   **Consequência:** confusão de identidade e scope pela credencial menos restritiva.

8. **Localização:** Intenção opaca de produção
   **Condição:** intenção é criada, procurada ou atualizada por identificador do browser sem vínculo completo.
   **Guarda:** intenção server-side com owner, sessão, ação, alvo, versão, uso único e TTL; lookup fora do vínculo falha fechado.
   **Consequência:** enumeração, troca ou reutilização para outro Project.

9. **Localização:** Formação de `ScopedReadContext`
   **Condição:** adaptador constrói diretamente o value object ou desserializa Tenant/Project de dados não reautorizados.
   **Guarda:** construtor restrito ao caso de uso autorizado e prova interna não serializável; proibir hidratação HTTP/claim.
   **Consequência:** contorno da resolução de membership antes do repositório.

10. **Localização:** Portas de repositório e cache
    **Condição:** cache, query builder, eager loading, telemetry ou port alternativo recebe recurso antes da autorização final.
    **Guarda:** exigir `ScopedReadContext` autorizado em toda porta de leitura, cache e Evidence; sem IDs soltos.
    **Consequência:** consulta/cache cross-project apesar de repositório protegido.

11. **Localização:** Sanitização de Evidence
    **Condição:** resposta inclui campos, relações, exceções, paginação ou URLs não allowlisted.
    **Guarda:** DTO/classificação allowlisted default-deny para relações, erros, headers, paginação e links.
    **Consequência:** Evidence bruta, IDs internos, URLs ou dados cross-tenant expostos.

12. **Localização:** Negações equivalentes
    **Condição:** status/corpo iguais, mas headers, tamanho, cache, timing, códigos internos ou retry diferem.
    **Guarda:** resposta, headers, cache, códigos observáveis e caminho de execução padronizados.
    **Consequência:** enumeração de Projects, memberships, recursos ou revogação.

13. **Localização:** Isolamento do harness fixture
    **Condição:** configuração, provider cacheado ou ambiente mal identificado habilita `InMemoryFixture*` fora de testes.
    **Guarda:** falhar no bootstrap sem processo de teste explicitamente atestado; não selecionar apenas por `APP_ENV`.
    **Consequência:** produção com autorização simplificada ou dados em memória.

14. **Localização:** Remoção de v1
    **Condição:** rotas, middleware, contratos, workers, caches ou proxies continuam aceitando/encaminhando `tracer/v1`.
    **Guarda:** inventariar/remover endpoints, bindings, parsers, fixtures, documentos e testes v1; teste negativo por entrada.
    **Consequência:** downgrade para claims antigas com scope.

15. **Localização:** Limites temporais
    **Condição:** `iat` futuro, `exp-iat` acima de 60 s, relógios divergentes ou token usado antes da emissão.
    **Guarda:** tipos inteiros, skew máximo explícito, `iat` não futuro, `exp` posterior, TTL ≤60 s e relógio confiável.
    **Consequência:** replay prolongado ou aceitação inconsistente entre instâncias.

16. **Localização:** BFF como fronteira exclusiva
    **Condição:** rota Laravel diretamente alcançável por internet, rede interna, browser ou serviço não-BFF.
    **Guarda:** restrição de rede e autenticação de serviço BFF mútua, identidade de chamador validada e teste de acesso direto.
    **Consequência:** contorno da fronteira Console/BFF.

## Revisão de segurança independente

### Cabeçalho

- **Revisão:** independente, da Story 1.3, SHA `f533fcaf3a427dd8ed0ce1b3ac53df6cb523398721e1dfbb4982489feb349f11`.
- **Tipo:** planejamento.
- **Limitação:** plano fornecido apenas; sem acesso a implementação, diffs, configuração, testes ou execução. Nenhuma verificação automatizada foi executada.

### Evidência de verificações

| Verificação | Evidência disponível | Resultado |
| --- | --- | --- |
| Separação de escopo de claims | v2 proíbe Tenant, Project, role e recurso; derive por membership | Adequado em intenção |
| Rejeição de transporte de scope | matriz cobre query, route, header, Authorization, cookie e body antes de Application/repo | Adequado em intenção; mecanismo não especificado |
| Uso único/revogação | `jti` atómico, TTL 60 s, revalidação sessão/membership/RBAC | Insuficiente para linearizabilidade/TOCTOU |
| Validação criptográfica | HMAC/assinatura citados sem algoritmo, `iss`, chave/kid, rotação ou comparação constante | Lacuna crítica |
| BFF e Laravel | política BFF-only existe, sem autenticação de serviço ou rede confiável | Lacuna relevante |
| Execução prevista | `setup_check`, verifier e testes previstos, sem resultados | Não verificado |

### 🔴 Alto Risco

#### SEC-1 — Perfil criptográfico v2 insuficientemente definido

- **Item / componente afetado:** verificador HMAC v2 e contrato de delegação.
- **Risco detectado:** algoritmo, `iss`, seleção/rotação de chave (`kid`), comparação constante e `iat`/skew não estão fechados.
- **Impacto para o projeto:** token forjado, assinado com algoritmo/chave indevidos ou reutilizado fora da janela, permitindo leitura cross-tenant/project.
- **Solução recomendada:** perfil fechado com algoritmo único, `iss` obrigatório, `kid` allowlisted com rotação controlada, rejeição de `alg` inesperado, comparação constante, validação de `iat`/`exp`/skew e audiência exata por operação; testes negativos para cada recusa.
- **Evidência:** o plano cita assinatura/audience/ação/correlação/tempo, sem controles criptográficos fundamentais.
- **Estado:** aberto — bloqueante.

#### SEC-2 — Atomicidade e TOCTOU do consumo de `jti`

- **Item / componente afetado:** consumo de `jti`, revalidação e acesso ao repositório.
- **Risco detectado:** corrida/TOCTOU entre reserva/consumo, revalidações e leitura; não há garantia sob múltiplas instâncias, reinício ou partição.
- **Impacto para o projeto:** replay concorrente, leitura após revogação ou contexto já inválido no repo.
- **Solução recomendada:** operação linearizável fail-closed com reserva/confirmação idempotente e TTL, autorização/contexto na mesma unidade de decisão imediatamente antes do repositório, comportamento documentado sob indisponibilidade/partição e testes multi-instância/revogação concorrente.
- **Evidência:** atomicidade é declarada, sem armazenamento, CAS/lock, semântica distribuída ou contenção TOCTOU.
- **Estado:** aberto — bloqueante.

#### SEC-3 — Fronteira BFF → Laravel sem autenticação de serviço

- **Item / componente afetado:** rota e fronteira Console/BFF → Laravel.
- **Risco detectado:** identidade de serviço e origem de chamada não estão especificadas.
- **Impacto para o projeto:** chamada direta ao Control Plane, falsificação de BFF ou superfície que contorna browser→BFF.
- **Solução recomendada:** autenticação mútua/de serviço, audience BFF→Laravel, identidade de workload, restrição de rede e autorização explícita de rota; negar antes da Application e testar browser/origem/credencial indevidos.
- **Evidência:** BFF-only é política do projeto, mas o plano não explica a distinção de um chamador direto.
- **Estado:** aberto — bloqueante.

### 🟡 Médio Risco

#### SEC-4 — Vínculo incompleto entre correlação e intenção opaca

- **Item / componente afetado:** `correlation_id` e intenção server-side.
- **Risco detectado:** não há vínculo explícito a sessão, principal, ação, intenção, alvo e requisição BFF.
- **Impacto para o projeto:** mistura de fluxos, replay contextual ou intenção para requisição diferente.
- **Solução recomendada:** persistir intenção de uso único com owner, sessão, ação, alvo, audience, correlação, expiração e estado; comparar todos os vínculos no consumo.
- **Evidência:** intenção e correlação são previstas, mas a relação completa não está definida.
- **Estado:** aberto.

#### SEC-5 — Construção/propagação de `ScopedReadContext`

- **Item / componente afetado:** value object e portas consumidoras.
- **Risco detectado:** não está inequívoca a impossibilidade de construção direta, serialização ou substituição por adaptadores/portas secundárias.
- **Impacto para o projeto:** bypass futuro de membership/RBAC, cache/Evidence/repo sem contexto autorizado.
- **Solução recomendada:** factory exclusiva do caso de uso, tipo imutável e não serializável; contexto obrigatório em todas as portas governadas, cache e Evidence.
- **Evidência:** mapa lista o value object sem restrição de construção/inventário de consumidores.
- **Estado:** aberto.

#### SEC-6 — Canais laterais em negações equivalentes

- **Item / componente afetado:** respostas HTTP, cache e observabilidade.
- **Risco detectado:** AC não inclui headers, cache-control, tamanho, timing e logs.
- **Impacto para o projeto:** enumeração de tenants, projetos, recursos, membership ou revogação.
- **Solução recomendada:** normalizar no primeiro adaptador, headers/cache idênticos, DTO allowlisted, logs redigidos e testes de equivalência de corpo, headers, cache e faixas de timing.
- **Evidência:** AC cita status/corpo/metadados apenas.
- **Estado:** aberto.

### 🟢 Baixo Risco

#### SEC-7 — Remoção integral da superfície v1

- **Item / componente afetado:** middleware, rotas, fixtures, contratos, caches e documentação.
- **Risco detectado:** sem dupla aceitação não prova remoção/invalidação de cada reconhecimento v1.
- **Impacto para o projeto:** regressão ou rota legada menos controlada.
- **Solução recomendada:** inventário, remoção/falha explícita e teste de busca/contrato de não aceitação.
- **Evidência:** substituição é declarada sem inventário verificável.
- **Estado:** aberto.

#### SEC-8 — Vazamento de fixtures in-memory

- **Item / componente afetado:** ServiceProvider e bootstrap de teste.
- **Risco detectado:** binding test-only pode escapar por configuração ambiental/boot inadequado.
- **Impacto para o projeto:** produção usa resolução artificial ou autorização permissiva.
- **Solução recomendada:** guard de bootstrap test-only, falha fora de testes e integração comprovando indisponibilidade em produção.
- **Evidência:** limitação está declarada sem mecanismo de impedimento.
- **Estado:** aberto.

### Cobertura STRIDE e governança

| Área | Resultado | Evidência / pendência |
| --- | --- | --- |
| Spoofing | Parcial | sessão/principal/audience previstos; fixar criptografia e autenticação BFF→Laravel |
| Tampering | Parcial | allowlist e rejeição previstas; normalizar HTTP e proibir contexto direto |
| Repudiation | Parcial | correlação e `jti` previstos; vínculo e persistência linearizável ausentes |
| Information Disclosure | Parcial | sanitização/allowlist previstas; cobrir headers, cache, timing e logs |
| Denial of Service | Parcial | TTL/limites previstos; definir reserva `jti`, expiração e store indisponível |
| Elevation of Privilege | Parcial | membership/Project/RBAC previstos; eliminar TOCTOU e exigir contexto em todas as portas |
| Segregação Dev × Security | Atendida no gate | revisor de segurança independente; execução posterior ainda exige evidência independente |
| Aprovação humana de arquitetura/plano | Pendente | definir dono/aprovação auditável para chaves, rotação e exceções |
| Política de artefatos/IDE e sandbox | Parcial | sem segredos em logs/Evidence; confirmar traces, dumps e IDE não registam dados sensíveis |

### Decisão

**BLOQUEADO.** Há três riscos altos abertos: perfil criptográfico v2 sem definição suficiente, atomicidade/TOCTOU sem garantia distribuída e BFF→Laravel sem autenticação de serviço definida. Não avançar para aprovação ou implementação até resolver os três pontos e convertê-los em critérios e testes negativos verificáveis.
