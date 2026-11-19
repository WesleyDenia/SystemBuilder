---
title: 'Derive membership scope at the application boundary'
type: 'feature'
ticket: '3'
created: '2026-11-19'
status: 'ready-for-dev'
route: 'full'
route_source: 'auto'
review: ''
review_source: ''
lenses_ran: []
review_loop_iteration: 0
context:
  - 'AGENTS.md'
  - '_bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/identity-membership-authorization-contract-v1.md'
  - '_bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md'
  - '_bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** O tracer atual transporta Tenant e Project em claims HMAC. Laravel precisa derivar scope de sessão/principal e membership autorizada, nunca de um chamador.

**Approach:** Introduzir principal, sessão e resolução de membership/Project em Application. A delegação v2 identifica principal, ação, audiência, correlação e tempo, mas nunca scope; Laravel revalida o estado e produz o único contexto utilizável antes do repositório.

**Decision (2026-11-19, autorização humana para corrigir o gate):** `tracer/v2` substitui `v1`, sem dupla aceitação. O envelope é três segmentos base64url sem padding: header fixo ASCII, payload JSON canónico e HMAC-SHA-256. Antes de assinar/verificar, parser lossless rejeita UTF-8 inválido, chaves repetidas, Unicode não canónico, `NaN`, float/expoente, tipos alternativos e payload cuja reserialização canónica não seja byte-a-byte idêntica. O schema estrito é `iss`, `kid`, `aud`, `sub`, `action`, `correlation_id`, `iat`, `exp`, `jti`; strings críticas são ASCII e comparadas byte-a-byte a enums de contrato. `aud` é tuplo fechado Control Plane+ambiente+operação. `iss` é literal; `kid` ASCII fixo é lookup exato em keyring por ambiente, com estado ativo/revogado, TTL limitado e invalidação distribuída imediata. Só há HMAC-SHA-256, assinatura em tempo constante, inteiros UTC, `iat <= now + skew`, `exp > now - skew` e `0 < exp-iat <= 60 s`; todo campo ausente, extra ou divergente falha. Tenant, Project, role e recurso não pertencem ao token. A delegação é emitida somente após Laravel autenticar a sessão do browser e é consumida somente com workload BFF mTLS autenticado; browser nunca recebe essa credencial. O fixture resolve um alvo único por principal, ação e correlação; produção exigirá intenção opaca ligada no servidor. Ausência, ambiguidade ou múltiplos alvos falha fechadamente, sem Project atual/fallback.

## Boundaries & Constraints

**Always:** Regras em `Domain`/`Application`, adaptadores em `Infrastructure`. Na emissão e consumo, validar sessão/principal/membership ativos, Project explícito e RBAC para ação; só essas fontes derivam scope. Intenção opaca é de uso único, expira em 60 s e vincula principal, `session_auth_epoch`, workload BFF, audience, ação, correlação, alvo e versões de membership/assignment/RBAC. Um único registro linearizável, namespaceado por ambiente, contém intenção e `jti`: `PENDING -> CONSUMED` ocorre em um CAS que compara todos os vínculos/versões, consome ambos terminalmente e retorna só êxito ou negação genérica; `REVOKED`/`EXPIRED` também são terminais. Não há segunda execução idempotente. O contexto carrega esse epoch e toda porta governada o confirma no mesmo limite consistente antes da leitura. Em timeout, partição, réplica ou store indisponível, falha fechado. No fixture, mutex único cobre snapshot, CAS e leitura; adaptador de produção não pode ser exposto até demonstrar a mesma garantia. Falhar fechado, sem disclosure; BFF não recebe autoridade Tenant/Project/role nem segredo do browser. Cada rota aceita uma única modalidade de autenticação e rejeita credenciais concorrentes. Cobrir cross-Tenant/Project e todos os seletores de transporte.

**Never:** Implementar fluxo completo de password/OTP/convites, UI de autenticação, MySQL/migrations ou o Ledger/outbox; expor uma rota Laravel ao browser; tornar o harness uma rota de produção; confiar em topologia, IP, `Forwarded`, `X-Forwarded-*` ou header de identidade para reconhecer BFF; aceitar `tracer/v1` como fallback ou claims de Tenant/Project/role; escolher Project por valor atual/primeiro/último; gravar credenciais, cookie, token, assinatura, claims, URL interna ou seletores manipulados em logs, Evidence ou respostas.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|---------------|----------------------------|----------------|
| Leitura autorizada | Delegação v2 e workload BFF mutuamente autenticado | Laravel revalida snapshot, reserva `jti` e resolve scope único | Leitura no mesmo limite consistente; resposta DTO sanitizada |
| Seletor adulterado | Query, rota, header, `Authorization`, cookie, body, duplicado ou forwarded propõe scope | Rejeitado antes da Application | Repositório não é chamado |
| Revogação ou corrida | Estado muda entre emissão/consumo ou há mesmo `jti` | Revalida e usa CAS; só um consumo com snapshot atual passa | Reserva falha/expira sem DoS; restantes negam antes do repositório |
| Autorização/alvo ausente | Sem membership, Project, RBAC ou intenção única | Sem acesso/Evidence positiva | Negação indistinguível |
| Delegação inválida | `alg`, `iss`, `kid`, schema, audiência, tempo, assinatura ou correlação inválidos | Nenhum contexto criado | Negação sanitizada, sem diferença observável |

</frozen-after-approval>

## Threat model (STRIDE)

| Ameaça / fluxo | Controlo e camada | Evidência exigida |
| --- | --- | --- |
| Spoofing | sessão/principal revalidados; HMAC-SHA-256/`iss`/`kid`/audience e workload BFF mútuo | identidade, assinatura, chave ou chamador inválido nega antes do repositório |
| Tampering | schema allowlist, normalização no primeiro adaptador, rejeição de duplicados/seletores/v1 | transporte/claims não alteram contexto |
| Repudiation | correlação/intenção vinculadas e `jti` CAS linearizável | corrida multi-instância aceita exatamente um consumo |
| Disclosure | DTO/Evidence allowlisted, logs redigidos, headers/cache/tamanho uniformes | negações equivalentes, sem segredo ou canal lateral material |
| DoS | limites token/request, reserva com TTL e fail-closed | excesso, tempo inválido, partição ou store indisponível negam |
| EoP | membership, Project e RBAC no mesmo snapshot; contexto de factory restrita | scope/alvo ausente ou contexto forjado não chega a porta alguma |

## Controlos de fecho do gate

### Token canónico e keyring

`contracts/tracer/v2` fixa header, ordem de campos, UTF-8/ASCII, tipos e serialização canónica; o verifier compara os bytes reserializados aos bytes recebidos antes de validar HMAC. `aud`, `iss`, `kid` e `action` pertencem a enums por deployment e não aceitam normalização, prefixo ou case-folding. O keyring é um port de Infrastructure com `kid` de formato fixo, chave ativa/revogada, versão e invalidação de cache; uma revogação durante TTL faz a delegação negar. Nenhum parser/framework alternativo recebe o payload antes da borda de validação.

### Consumo terminal e contexto capability

`DelegationConsumptionStore::consumeIfCurrent(...)` recebe intenção, `jti` e todos os bindings/epochs; faz a única transição CAS e só então entrega um `ScopedReadContext` capability imutável, não serializável e de factory privada. Repositório, cache, lazy loading e Evidence não expõem método que aceite IDs/contexto opcional: confirmam o epoch da capability antes de tocar dados. O teste instrumenta todas essas portas e falha se qualquer uma for chamada num denial. Quotas de tamanho, taxa e cardinalidade por principal, sessão e workload antecedem criação/reserva; métricas usam apenas correlação opaca, código de política e workload pseudonimizado.

### Workload BFF e negação uniforme

`BffWorkloadAuthenticator` exige mTLS no próprio limite Laravel: cadeia contra trust bundle por ambiente, EKU clientAuth, SAN/SPIFFE exato `spiffe://systembuilder/<env>/console-bff`, validade e revogação/rotação da identidade. Só esse SAN mapeia para as audiências/operações BFF declaradas; qualquer proxy deve preservar peer identity criptograficamente verificável, ou o bootstrap da rota falha. Headers de identidade, rede privada e CORS não são evidência de BFF. O middleware corre antes de logging, binding, rate limiter, parsing de body ou exceções detalhadas. Toda negação usa envelope único sem `Set-Cookie`, trailer, redirect, metadado variável ou efeito secundário, com status/corpo/headers/cache/tamanho e faixa de latência testados como equivalentes.

## Code Map

- `control-plane/app/Domain/Tenancy/ScopedReadContext.php` -- value object imutável, não serializável, com factory restrita ao caso de uso autorizado; nunca é seletor HTTP/claim.
- `control-plane/app/Application/Tenancy/ReadScopedTracerFixture.php` e `Ports/` -- principal, sessão, membership/RBAC, intenção/snapshot/epoch; toda porta de leitura/cache/Evidence aceita capability validada.
- `control-plane/app/Application/Tenancy/Ports/{FixtureDelegationVerifier,DelegationConsumptionStore,BffWorkloadAuthenticator,Keyring}.php` -- v2 canónico, perfil HMAC/keyring, BFF mTLS e CAS conjunto intenção+`jti`.
- `control-plane/app/Infrastructure/Tenancy/{InMemoryFixtureMembershipAuthorizer,InMemoryFixtureDelegationReplayGuard,FixtureScopedReadRepository}.php` -- mutex de reautorização-reserva-leitura e bindings test-only que fazem bootstrap falhar fora de processo de teste atestado.
- `control-plane/app/Providers/AppServiceProvider.php` -- ligar ports apenas ao harness de teste; nunca selecionar fixture por `APP_ENV` editável.
- `control-plane/app/Http/Middleware/AuthenticateBffWorkload.php`, `Controllers/TracerScopedReadController.php`, `Requests/TracerDelegationRequest.php` e `routes/api.php` -- mTLS/workload antes de qualquer input/log/binding, rota privada e normalização/rejeição precoce; sem construção de scope por transporte.
- `control-plane/tests/Feature/TracerScopedReadTest.php`, `scripts/verify-tracer.mjs` e `contracts/tracer/v2/*` -- migrar v1, cobrir revogação/corrida/negações equivalentes e provar que transporte não altera scope.
- `console/app/api/tracer/route.ts`, `console/bff/tracer-client.ts` e `console/server/tracer-config.ts` -- manter como cliente server-to-server de teste, sem decisões de Tenant/Project/membership e sem variáveis públicas.

## Tasks & Acceptance

**Execution:**
- [ ] `control-plane/app/Domain/Tenancy/` e `Application/Tenancy/Ports/` -- factory/contexto capability restritos, principal/sessão/membership/RBAC, epochs e intenção vinculada; nunca scope em claims.
- [ ] `ReadScopedTracerFixture.php` e adapters tenancy -- parser canónico, keyring revogável e `consumeIfCurrent` CAS único de intenção+`jti`/snapshot/epoch antes e durante leitura; sem réplica/fallback.
- [ ] `AppServiceProvider.php`, middleware, controller, FormRequest e rotas -- binding de teste atestado, mTLS SAN/SPIFFE BFF, rota privada e normalização de credenciais/transportes antes de logging/binding.
- [ ] testes Laravel -- serialização divergente/duplicados/Unicode/tipos, algoritmo/`iss`/`kid`/keyring/assinatura/tempo, workload/certificado/rotação, intenção+`jti` CAS, epochs, quotas, selectors, revogação/partição/corrida multi-instância; instrumentar repo/cache/Evidence e exigir zero chamada negada.
- [ ] Console, verifier e `contracts/tracer/v2/*` -- inventariar/remover v1 em cada entrada; limitar inputs, provar BFF-only, intenção/correlação vinculadas, auditoria minimizada e envelope de negação integral (corpo, headers, cookie, trailer, cache, tamanho, timing) sem redação reversível.

**Acceptance Criteria:**
- Given envelope canónico v2 HMAC-SHA-256, `iss`/`kid`/audience/tempo corretos, SAN/SPIFFE BFF autorizado e sessão/principal/membership/Project/RBAC ativos, when uma leitura scoped é executada, then Laravel revalida snapshot/epochs, faz CAS terminal único de intenção+`jti` e chama todas as portas somente com capability de factory restrita.
- Given JSON/UTF-8/tipo não canónico, chave duplicada, algoritmo, `iss`, `kid`, keyring revogado, assinatura, `iat`/`exp`/skew, audience, ação, correlação/intenção, certificado/workload BFF, Tenant, Project, role, claim inesperada ou `v1` inválidos, when o pedido chega, then falha na borda antes de logging, Application, cache, Evidence ou repositório e não altera scope.
- Given logout/epoch alterado, revogação, remoção de membership/Project/RBAC, token vencido, quota excedida, store indisponível/particionado ou tentativas multi-instância com mesma intenção/`jti`, when a operação é avaliada, then falha fechadamente antes da leitura; um único CAS com snapshot/epochs atuais pode passar.
- Given negações por identidade, membership, Project, ação ou recurso fora de scope, when observadas externamente, then status, corpo, headers, cookies, trailers, cache, tamanho, redirects e faixa de timing permanecem equivalentes e não expõem segredo, URL interna ou contexto autorizado.

## Implementation Notes

## Plan Change Log

- 2026-11-19 — O gate adversarial+segurança bloqueou a revisão `c8e472…304c` por STRIDE ausente, revogação/replay não implementáveis e contrato de claims/audience ambíguo. O plano passa a exigir `tracer/v2` sem fallback, schema estrito sem claims de scope, revalidação/consumo atómico, intenção de alvo server-side, matriz STRIDE e provas de negação equivalentes. Evita que o harness transforme claims ou seletores em autoridade.
- 2026-11-19 — Após autorização humana, o gate bloqueado `f533fcaf3a42…` foi tratado no plano: perfil criptográfico HMAC fechado, `kid`/tempo/audience estritos, identidade de workload BFF mutuamente autenticada, intenção totalmente vinculada, CAS linearizável/snapshot e guards test-only. A revisão seguinte deve verificar esses controles, sem reutilizar o relatório anterior.
- 2026-11-19 — Após nova autorização humana, os três bloqueios de `a5c516cee646…` foram incorporados: envelope canónico/strict parser e keyring revogável; CAS terminal único para intenção+`jti` com epochs; mTLS SAN/SPIFFE e mapeamento explícito de workload BFF. Também foram fechados quotas, capability em todas as portas, envelope integral de negação e auditoria sanitizada. Esta é a revisão candidata a encerramento e aceitação humana, condicionada ao novo gate.

## Review Triage Log

## Design Notes

O contexto de autorização é produzido uma vez por factory interna do caso de uso, a partir de snapshot/epochs de sessão, principal, membership, RBAC e intenção server-side; somente essa capability chega a repositório, cache ou Evidence. A delegação v2 identifica principal/ação, não substitui lookup de membership e não carrega scope. O registro terminal liga intenção e `jti`; o CAS valida bindings/epochs e entrega a capability somente no caminho vencedor. O fixture tem alvo/mutex apenas por ser test-only; qualquer adaptador de produção precisa demonstrar persistência/consistência equivalentes e mTLS verificável antes de ser exposto, sem deslocar policy para BFF ou HTTP.

## Verification

**Commands:**
- `uv run _bmad/scripts/setup_check.py` -- expected: diagnóstico BMAD sem erros de configuração.
- `sh scripts/verify-tracer.sh` -- expected: Compose executa PHPUnit e o verifier; leituras autorizadas passam e todas as negações de contexto/delegação são sanitizadas, com zero fuga de segredo.
- `docker compose run --rm control-plane-tests` -- expected: testes Laravel cobrem derivação de scope e não permitem invocação de repositório em cada caminho negado.
