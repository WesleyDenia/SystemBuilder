# Reconciliação: Open Questions

Fonte reconciliada: `spec-systembuilder-control-plane-kernel/open-questions.md`  
Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`

## Cobertura forte

- Import/adoption está explicitamente limitado por A-7 e reaberto em OQ-14.
- Dependency graph, regras `unchanged / REQUIRES_REVIEW / STALE`, exceção formal de `BLOCKED` e checks `FAIL` overrideable estão cobertos por OQ-2, OQ-4 e OQ-12.
- Versão BMAD inicial, inventário mínimo e Contract Tests aparecem em OQ-8; provider/executor e Capability obrigatórias/opcionais aparecem em OQ-9.
- Schema, imutabilidade, versionamento, retenção, exportação, redaction e recovery estão cobertos por OQ-3; cardinalidade e ownership estão em OQ-1.
- A realidade end-to-end de Coding/Story e integração mínima com repositório/checks estão em OQ-10; integrações completas permanecem no addendum como adiadas.
- Segredos, credenciais, código, prompts, logs e Evidence estão em OQ-6; Product Discovery, buyer, pricing, jurídico, compliance completo e GTM estão explicitamente fora do PRD, conforme a restrição da sessão.
- As notas de conflito da fonte foram absorvidas: o PRD estreita ideia→deploy para o Kernel, rejeita seleção de stack e usa a tese de assurance sem decidir GTM.

## Gaps

1. **Beachhead foi removido da lista de Open Questions sem registro individual de disposição.** O parágrafo final da seção 12 o coloca genericamente fora do PRD, mas não deixa um ID/rastreio de que continua aberto e não bloqueia o Kernel.
2. **Graduação das métricas de produto ficou parcialmente resolvida por seleção unilateral.** O PRD seleciona SM-1–SM-6 como invariantes do Kernel e adia metas de eficiência, mas não registra uma OQ sobre validação/graduation dos sinais instrumentados após amadurecimento das jornadas.
3. **Inventário BMAD completo perdeu rastreio explícito.** OQ-8 trata o inventário mínimo do primeiro build e o addendum adia o inventário completo, mas não há uma questão identificada sobre mapping skill-by-skill de native state, Artifact, Gate, memlog e tracking.
4. **A suficiência dos Contract Tests do Adapter não está formulada como pergunta separada.** FR-16/FR-17 exigem testes, porém OQ-8 não define quais provas cobrem com segurança as seis operações `inspect`, `validate_entry`, `invoke`, `collect`, `reconcile` e `project_state`.
5. **Políticas para acesso futuro Git/deploy não têm disposição rastreável.** Isso é coerente com o recorte, mas deve constar como diferido, separado do credential scope necessário ao Coding/Story inicial.

## Conflitos

- A fonte mantém beachhead e seleção final de métricas como perguntas de produto; o PRD as exclui do recorte e, para o Kernel, promove seis invariantes a métricas primárias. A reconciliação correta é distinguir métricas de conformidade do Kernel de métricas de produto/mercado ainda abertas.
- A nota de conflito afirma que a arquitetura anterior propõe Laravel/Next.js/Hetzner; o companion `architecture-reference.md` presente só cita Laravel nominalmente. O PRD/addendum corretamente não transforma nenhuma dessas tecnologias em decisão.
- Questões de licença, IP, privacidade e compliance permanecem abertas na fonte, mas o pedido do PRD determina que não sejam resolvidas agora. Mantê-las como dependências externas/diferidas é mais fiel que tratá-las como bloqueadores do Kernel conceitual.

## Correções recomendadas

- Criar um pequeno índice de “Open Questions deliberadamente externas” com IDs para beachhead, métricas de produto/mercado, jurídico e compliance; marcar todas como não bloqueantes para a arquitetura do Kernel.
- Separar SM-1–SM-6 como métricas de conformidade/integridade e adicionar uma OQ para seleção de metas de eficiência e resultados de produto após baseline instrumentada.
- Desdobrar OQ-8 em: target BMAD inicial; inventário mínimo do recorte; inventário completo diferido; e suficiência da suíte de Contract Tests para as seis operações.
- Registrar separadamente o credential scope mínimo de AI provider/repositório para Coding/Story e o acesso deploy/produção explicitamente fora do MVP.
