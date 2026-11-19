# Gate de revisão do plano — Story 1.3

- **Plano revisto:** `story-derive-membership-scope-at-the-application-boundary-plan.md`
- **Revisão SHA-256:** `c8e47251de53665698e2912322e6c15e4e0b9a87d0eedd889adacea016dd304c`
- **Data:** 2026-11-19
- **Tipo:** planejamento
- **Independência:** os achados adversariais e a decisão de segurança foram produzidos por revisores independentes do autor do plano. A primeira tentativa de revisão não tinha acesso ao artefato e foi descartada; não constitui evidência deste gate.
- **Decisão do gate:** **BLOQUEADO** — o plano mantém o estado `draft`; não pode seguir para aprovação, `ready-for-dev` ou implementação.

## Revisão adversarial

1. **Definição de tipos e ports** — o port pode devolver scope apenas pela identidade, sem exigir ação, membership ativa e atribuição explícita ao Project. Definir `resolveScope(principalId, action)` para negar sem estes três controlos; caso contrário, membership de Tenant pode abrir Projects não atribuídos.
2. **Estado de sessão/delegação** — a validação pode ocorrer só na emissão ou só no consumo. Exigir sessão ativa e não revogada em ambos os momentos; tokens emitidos antes de logout, remoção ou revogação não podem continuar utilizáveis.
3. **Adaptador HMAC** — claims de Tenant, Project ou role podem sobreviver e ser reutilizados. O schema permitido deve transportar somente identidade/principal, ação, audience, correlação, `jti` e expiração; claims de scope devem ser rejeitadas ou inequivocamente não autorizativas.
4. **Use case** — o repositório pode ser chamado para resolver alvo antes da reautorização. A autorização deve produzir o contexto completo antes de qualquer acesso a Infrastructure, evitando enumeração por tempo, erros ou existência.
5. **FormRequest/controller** — seletores de scope ainda podem alcançar a Application. Validar somente o payload permitido e rejeitar seletores de scope inesperados em vez de os tornar disponíveis a casos de uso futuros.
6. **Rotas Laravel** — o plano não define o controlo que impede cliente/browser direto. Especificar autenticação serviço-a-serviço, delegação server-only e teste de negação direta.
7. **Audience** — a audience precisa estar ligada ao destinatário Laravel e à operação declarada. Sem isso, uma delegação pode ser reutilizada noutro ambiente/serviço/operação.
8. **Correlação e uso único** — falta definir consumo atómico de `jti` antes da leitura. O operador precisa de unicidade, expiração e correlação para que corridas aceitem no máximo um pedido.
9. **Expiração** — o limite de 60 segundos não está como requisito verificável. Definir `iat`/`exp`, tolerância de relógio limitada e rejeição de token futuro ou de vida excessiva.
10. **Sessão** — “sessão inativa” não cobre logout, rotação, desativação do principal e remoção de membership. Definir as transições que invalidam delegações existentes.
11. **RBAC** — membership e ação são citadas, mas falta o port/regra que aplica papel à ação. Autorizar somente por membership ignora privilégio mínimo.
12. **Resolução de Project** — não permitir “Project atual”, primeiro Project ou último usado como fallback. Um alvo opaco server-side sem resolução inequívoca deve falhar fechado.
13. **Intenção do recurso** — o plano não explica como identificar o alvo sem confiar em Tenant/Project do BFF. Vincular uma referência opaca de intenção/correlação a principal e ação no servidor.
14. **Negações** — exigir equivalência observável entre inexistente, cross-scope, revogado e sem atribuição; a mera ausência de fixture no corpo não basta.
15. **Logs e Evidence** — definir allowlist/redação para token, cookie, assinatura, claims, URLs e seletores manipulados.
16. **Superfícies HTTP** — cobrir route params, `Authorization`, `X-Forwarded-*`, valores duplicados, content types alternativos e JSON aninhado, além de query/header/cookie/body simples.
17. **Revogação e concorrência** — testar revogação entre emissão e consumo, remoção de Project, alteração RBAC e consumo paralelo do mesmo `jti`.
18. **Verificação canónica** — provar explicitamente que browser/cliente direto é negado e que Laravel nunca deriva scope de transporte, não apenas que o verifier atual passa.

## Revisão de Segurança

### Cabeçalho

- **Data e revisor independente:** 2026-11-19; Vex, revisor de segurança independente.
- **Escopo e versão/revisão:** plano da story 1.3, SHA-256 `c8e47251de53665698e2912322e6c15e4e0b9a87d0eedd889adacea016dd304c`.
- **Tipo:** planejamento; nenhuma implementação avaliada.
- **Materiais não acessíveis / limitações:** sem execução de testes, SAST/SCA ou scans; esta limitação não equivale à ausência de vulnerabilidades.

### Evidência de verificações

| Verificação | Alvo | Resultado | Limitação |
|---|---|---|---|
| Laravel como autorizador final | plano | parcialmente coberto por reautorização antes do repositório | sem implementação para validar |
| Browser somente via Console/BFF | plano | parcialmente coberto; controlo concreto não definido | sem teste de cliente direto |
| Scope não concedido por input | plano | coberto como intenção | falta normalização e schema vinculativo |
| Anti-replay e expiração | plano | não verificável como contrato executável | faltam atomicidade, TTL e casos de teste |
| STRIDE explícito | plano | ausente | bloqueador de governança |

### 🔴 Alto Risco

#### STRIDE ausente para fluxos materiais

- **Item / componente afetado:** plano inteiro, secções de ameaças e verificação.
- **Risco detectado:** spoofing, tampering, repudiation, disclosure, denial of service e elevation of privilege permanecem implícitos.
- **Impacto para o projeto:** delegação, identidade, Tenant, Project, ação ou correlação podem chegar ao repositório sob condições indevidamente aceites.
- **Solução recomendada:** acrescentar matriz STRIDE para emissão, transporte BFF→Laravel, sessão/principal, resolução membership/Project/ação, consumo atómico, acesso a repositório, negação, logging/Evidence e revogação. Para cada ameaça, declarar controlo, camada, teste negativo e evidência esperada.
- **Evidência:** o plano tem princípios e matriz funcional, mas não mapeia ameaças aos fluxos.
- **Estado:** aberto — bloqueia pronto para desenvolvimento.

#### Revogação e consumo concorrente sem contrato implementável

- **Item / componente afetado:** ports de sessão/delegação, emissor/verificador e armazenamento de uso único.
- **Risco detectado:** token emitido antes de logout, desativação ou remoção de membership pode permanecer utilizável; corridas podem permitir consumo múltiplo.
- **Impacto para o projeto:** acesso pós-revogação, repetição de delegação ou operação fora de scope.
- **Solução recomendada:** validar sessão/principal/membership/Project/ação na emissão e consumo; invalidar em logout, rotação, desativação e revogação; definir consumo `jti` atómico, TTL máximo de 60 segundos, correlação e falha fechada; testar revogação entre emissão/consumo e concorrência.
- **Evidência:** uso único e expiração são citados, mas sem semântica, autoridade, atomicidade ou cenários obrigatórios.
- **Estado:** aberto — bloqueia pronto para desenvolvimento.

#### Contrato de identidade e audience insuficientemente delimitado

- **Item / componente afetado:** contratos v1, verifier e configuração tracer/BFF.
- **Risco detectado:** token pode preservar claims de Tenant, Project ou papel reutilizáveis; audience genérica permite confusão de destinatário/operação.
- **Impacto para o projeto:** elevação de privilégio e atravessamento de scope.
- **Solução recomendada:** remover claims de scope do transporte ou declará-las não autorizativas; ligar audience ao destinatário Laravel e ação específica; rejeitar claims inesperadas, ausentes, duplicadas ou conflitantes e proibir fallback de Project.
- **Evidência:** o problema reconhece claims de scope, mas o plano não fixa schema permitido/proibido nem semântica de audience.
- **Estado:** aberto — bloqueia pronto para desenvolvimento.

### 🟡 Médio Risco

#### Manipulação HTTP sem normalização definida

- **Item / componente afetado:** controller, FormRequest, routes e BFF.
- **Risco detectado:** parâmetros de rota, `Authorization`, headers encaminhados, chaves duplicadas, JSON ambíguo ou cookies podem ser interpretados de formas divergentes.
- **Impacto para o projeto:** bypass de validação, confusão de identidade ou divulgação lateral.
- **Solução recomendada:** definir contrato de entrada/normalização, rejeitar seletores/duplicados/ambiguidade, não confiar em headers forwarded e testar todos os vetores.
- **Evidência:** os vetores surgem apenas como intenção de teste.
- **Estado:** aberto.

#### Observabilidade e Evidence sem requisitos operacionais

- **Item / componente afetado:** logs, tracer, erros e Evidence.
- **Risco detectado:** correlação, URL, contexto ou claims podem vazar.
- **Impacto para o projeto:** divulgação de metadados governados, material de delegação ou enumeração.
- **Solução recomendada:** definir allowlist e redação antes de logar; proibir payload/header/token/URL sensível; padronizar respostas indistinguíveis e Evidence sanitizada.
- **Evidência:** há proibição genérica, sem redator, allowlist ou teste.
- **Estado:** aberto.

#### Negação direta do browser sem mecanismo verificável

- **Item / componente afetado:** routes e autenticação Laravel.
- **Risco detectado:** uma rota Laravel publicamente acessível pode ser invocada fora do BFF.
- **Impacto para o projeto:** bypass da fronteira Console/BFF.
- **Solução recomendada:** especificar autenticação serviço-a-serviço exclusiva do BFF, audience obrigatória, origem não falsificável e teste de cliente direto; não confiar em IP ou `X-Forwarded-*`.
- **Evidência:** a intenção de negar browser direto não define o controlo.
- **Estado:** aberto.

### 🟢 Baixo Risco

#### Negações ainda não exigem equivalência observável

- **Item / componente afetado:** critérios de aceitação e testes de integração.
- **Risco detectado:** status, corpo, tempo ou correlação podem distinguir ausência de membership, Project, ação ou recurso.
- **Impacto para o projeto:** enumeração limitada de recursos e memberships.
- **Solução recomendada:** adicionar testes de equivalência observável, preservando correlação interna sanitizada.
- **Evidência:** o plano exige “sem confirmação”, mas não define critério mensurável.
- **Estado:** aberto.

### Cobertura STRIDE e governança

| Área | Resultado | Evidência / pendência |
|---|---|---|
| Spoofing | parcial | definir fonte de identidade, autenticação BFF→Laravel, rotação e logout |
| Tampering | parcial | normalização e rejeição de duplicados, route params, forwarded e conflito de claims |
| Repudiation | parcial | auditoria sanitizada, imutável e sem segredos/PII |
| Information Disclosure | parcial | allowlist/redação em logs, tracer e Evidence; respostas indistinguíveis |
| Denial of Service | ausente | limites de tamanho/frequência, armazenamento JTI, expiração e falha de dependência |
| Elevation of Privilege | parcial | RBAC/ação, membership ativa, Project explícito, sem fallback e revalidação no consumo |
| Segregação Dev × Security | coberta | revisor independente do autor do plano |
| Aprovação humana de arquitetura/plano | pendente | correções do plano precisam de decisão humana |
| Política de artefatos/IDE e sandbox | não verificável | sem acesso ao ambiente de execução/IDE |

### Decisão

**BLOQUEADO.** Não existe STRIDE explícito para os fluxos materiais e faltam requisitos implementáveis de revogação/consumo concorrente e schema de claims/audience. A próxima ação é obter aprovação humana para corrigir o plano, produzir uma nova revisão e repetir integralmente este gate.
