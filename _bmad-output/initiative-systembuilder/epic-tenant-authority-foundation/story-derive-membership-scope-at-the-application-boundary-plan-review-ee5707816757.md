# Gate de revisão do plano — Story 1.3

- **Data:** 2026-11-19
- **Plano revisto:** `story-derive-membership-scope-at-the-application-boundary-plan.md`
- **SHA-256 do plano:** `ee570781675751cfc6bf3d62f0094b125ade3e87c06eef56a8f6e12828627a6f`
- **Sequência obrigatória:** adversarial → segurança independente
- **Independência:** as duas revisões foram conduzidas por revisores independentes do autor do plano; a security review avaliou também os achados adversariais.
- **Escopo:** planejamento. Não houve implementação, configuração, infraestrutura ou teste executável acessível nesta revisão.

## Decisão do gate e aceitação humana

**CONCLUÍDO E ACEITE COM RESSALVAS.** Não há Alto Risco aberto no desenho revisado. A revisão de segurança concluiu **APROVÁVEL COM RESSALVAS**. O responsável humano autorizou explicitamente a incorporação dos três controlos e o encerramento/aceitação da revisão; essa decisão aceita as ressalvas médias e baixas abaixo somente para o planejamento. Implementação, configuração e testes de segurança continuam obrigatórios antes de qualquer promoção operacional.

## Revisão adversarial

1. **Custódia HMAC:** BFF não pode possuir chave de assinatura; Laravel é o custodiante exclusivo. Caso contrário, BFF comprometido forja `sub`, ação, audiência e `jti`.
2. **mTLS em toda rota:** toda rota de workload deve exigir identidade de peer verificada e SAN/SPIFFE exato; bootstrap isolado não basta.
3. **Revogação de certificado:** CRL/OCSP/trust bundle ausente ou stale deve negar. Sem isso, certificado revogado continua válido.
4. **Base64url:** decoder deve rejeitar padding, caracteres fora do alfabeto e representações não canónicas; comparar o segmento à re-encodificação canónica.
5. **JSON canónico:** perfil de ordenação, escaping, NFC e números precisa ser definido; divergência entre emissores cria bypass/falhas.
6. **Limites de claims:** `sub`, `jti`, `correlation_id` e strings precisam de tamanho, alfabeto e proibição de control chars.
7. **Alvo da intenção:** resolver e reautorizar o alvo no Laravel; não aceitar target derivado de browser/BFF sem validação.
8. **Mutações governadas:** para escrita futura, consumo da intenção, mutação, Ledger e outbox devem ser atómicos para não queimar intenção sem resultado.
9. **Cache:** chave deve conter tenant, Project e versões de membership/RBAC/assignment para impedir reutilização cross-scope.
10. **Portas de escrita:** capability deve ser confirmada também para escrita, delete, lock, outbox, fila e storage.
11. **Workers/callbacks:** consumidor assíncrono recebe referência server-side e reautoriza/emite capability nova; não serializa capability.
12. **Borda HTTP:** rejeitar seletores proibidos sobre mensagem bruta antes de CDN/WAF/proxy/framework criarem representação divergente.
13. **Quotas:** reservas devem ser atómicas, expirar/libertar e incidir antes de criptografia custosa para não permitir exaustão.
14. **Equivalência de negações:** definir orçamento/método de latência para TLS, parser, keyring, store e aplicação.
15. **Enforcement arquitetural:** testes de arquitetura/CI devem impedir clientes diretos de DB/ORM/Redis/S3/HTTP fora de adapters capability-aware.

## Revisão de segurança independente

### Cabeçalho

- **Escopo:** somente planejamento da revisão `ee570781675751cfc6bf3d62f0094b125ade3e87c06eef56a8f6e12828627a6f`.
- **Limitação:** não havia código, configuração, testes executáveis ou evidência de implementação; os controlos permanecem não verificados até a implementação.

### Evidência de verificações

| Verificação | Evidência no plano | Resultado |
| --- | --- | --- |
| Autorização/escopo | Laravel final, scope fora do token, intenção versionada e capability privada | Coberto em desenho |
| BFF → Laravel | mTLS, SAN/SPIFFE, EKU, rotação/revogação, fail-closed | Coberto em desenho |
| Integridade | HMAC-SHA-256, comparação constante, parser lossless, claims fechadas, TTL | Coberto em desenho |
| Replay/concorrência | registro linearizável, `consumeIfCurrent`, CAS e estados terminais | Coberto em desenho |
| Multi-tenant | seletor HTTP negado na borda, ports sem IDs/contexto opcional | Coberto em desenho |
| Segredos/observabilidade | sem segredo no browser/log/Evidence; auditoria/métricas sanitizadas | Coberto em desenho |
| DoS | quotas, TTL, limites e fail-closed | Coberto em desenho |
| Verificação real | nenhum artefato executável acessível | Pendente de implementação/testes |

### 🔴 Alto Risco

**Nenhum achado aberto no escopo.** A aceitação humana não reduziria um Alto Risco; os controlos materiais estavam definidos no nível de planejamento avaliado.

### 🟡 Médio Risco — aceites pelo responsável humano

#### SEC-1 — Perfil canónico normativo

- **Item / componente afetado:** base64url/JSON do token.
- **Risco detectado:** implementações podem aceitar serializações distintas do mesmo conteúdo assinado.
- **Impacto:** ambiguidade de validação, divergência entre nós e potencial bypass.
- **Solução recomendada:** fixar perfil interoperável (alfabeto base64url, sem padding/bits não canónicos, limites de comprimento e referência a perfil JSON canónico, como RFC 8785, com restrições adicionais).
- **Evidência:** o plano exige base64url sem padding e JSON canónico, mas não nomeia o perfil normativo completo.
- **Estado:** aceite com ressalva para planejamento.

#### SEC-2 — Reautorização do alvo e mutações futuras

- **Item / componente afetado:** `target`, escritas, Ledger/outbox.
- **Risco detectado:** o alvo está vinculado, sem invariantes explícitos de resolução Laravel-side e atomicidade para cada mutação futura.
- **Impacto:** alvo removido/reassociado ou mutação sem facto auditável atómico.
- **Solução recomendada:** reautorizar alvo imediatamente antes da operação e, em escritas/deletes/jobs/callbacks, consumir intenção, mutar, Ledger e outbox na mesma fronteira transacional.
- **Evidência:** binding de target/capability/CAS está no plano, sem extensão explícita para toda mutação futura.
- **Estado:** aceite com ressalva para planejamento.

#### SEC-3 — Bypass arquitetural

- **Item / componente afetado:** uso direto de infraestrutura.
- **Risco detectado:** instrumentar ports não prova ausência de clientes DB/ORM/Redis/S3/HTTP diretos.
- **Impacto:** escrita governada pode contornar capability, Gate, auditoria ou isolamento.
- **Solução recomendada:** testes de arquitetura/CI que proíbam clientes de infraestrutura em Domain/Application e escrita governada fora de ports/casos de uso autorizados.
- **Evidência:** ausência de regra de teste explícita.
- **Estado:** aceite com ressalva para planejamento.

### 🟢 Baixo Risco — aceites pelo responsável humano

#### SEC-4 — Limites de identificadores críticos

- **Risco:** sem máximos explícitos para `sub`, `jti`, `correlation_id` e control chars.
- **Impacto:** amplificação de armazenamento/log ou inconsistência de normalização.
- **Solução:** definir tamanhos máximos, charset e rejeição de control chars antes de parsing/persistência.
- **Evidência:** plano fecha tipos/ASCII, sem todos os limites.
- **Estado:** aceite com ressalva para planejamento.

#### SEC-5 — Latência observável

- **Risco:** equivalência de timing sem orçamento/método mensurável.
- **Impacto:** enumeração residual entre camadas.
- **Solução:** definir orçamento, pontos de medição e testes estatísticos por rota/camada.
- **Evidência:** plano exige timing equivalente/testes exatos.
- **Estado:** aceite com ressalva para planejamento.

#### SEC-6 — Chaves de cache

- **Risco:** cache de decisão pode omitir tenant/Project/versões/epoch.
- **Impacto:** decisão reutilizada entre scopes/versões.
- **Solução:** incluir tenant, Project, membership/RBAC/policy version e epoch; invalidar fail-closed.
- **Evidência:** keyring é invalidado; chave de autorização não está expressa.
- **Estado:** aceite com ressalva para planejamento.

### Cobertura STRIDE e governança

| Área | Resultado | Evidência / ressalva |
| --- | --- | --- |
| Spoofing | Coberta | mTLS, EKU, SAN/SPIFFE, trust bundle, rotação/revogação |
| Tampering | Coberta com ressalva | HMAC/parser/claims/CAS; falta perfil canónico nomeado |
| Repudiation | Coberta com ressalva | auditoria sanitizada; mutações devem acoplar Ledger/outbox |
| Information Disclosure | Coberta | sem segredo; negação uniforme e métricas sanitizadas |
| Denial of Service | Coberta com ressalva | TTL/quotas/fail-closed; limites de identificadores pendentes |
| Elevation of Privilege | Coberta com ressalva | scope fora do token/capability; reautorização de alvo em mutações futuras |
| Segregação Dev × Security | Coberta | revisões independentes separadas do autor |
| Aprovação humana | Registada | responsável humano aceitou controlos e ressalvas do planejamento |
| Política de artefatos/IDE e sandbox | Coberta em desenho | sem segredos, runner isolado, sem raw HTTP nos ports; execução ainda pendente |

### Decisão da security review

**APROVÁVEL COM RESSALVAS.** Não há Alto Risco aberto no plano. As três ressalvas médias e três baixas foram explicitamente aceites pelo responsável humano para a fase de planejamento; implementação/configuração/testes de segurança permanecem obrigatórios antes de promoção operacional.
