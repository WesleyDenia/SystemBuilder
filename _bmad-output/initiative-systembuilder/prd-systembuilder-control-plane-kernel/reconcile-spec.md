# Reconciliação — SPEC do Control Plane Kernel

Fonte reconciliada: `../spec-systembuilder-control-plane-kernel/spec-systembuilder-control-plane-kernel.md`.

Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`.

## Cobertura forte

- As dez capabilities da SPEC estão materialmente cobertas: Stage Contract (FR-2/FR-3), Readiness (FR-4), Override (FR-6), Assurance Core (FR-10), reabertura (FR-14), impacto downstream (FR-15), Workflow declarativo (FR-7), ledger/rastreabilidade (FR-11/FR-12), BMAD Adapter (FR-16/FR-17) e os três Stage Profiles (FR-18 a FR-20).
- As fronteiras e invariantes centrais foram preservadas em PD-1 a PD-10 e NFR-1 a NFR-10: Control Plane governa, Execution Plane executa, `COMPLETED` depende do Exit Contract, `BLOCKED` não tem bypass genérico, Baseline concluída não é sobrescrita e regras de produto não dependem de provider ou internals BMAD.
- As premissas explícitas da SPEC aparecem em A-4 a A-6: um operador primário, uma macro Stage ativa, serialização em escopos conflitantes, `Guided + Supervised`, distinção entre `REQUIRES_REVIEW` e `STALE` e Adapter inicialmente conceitual.
- Os non-goals foram preservados em §6.2; o addendum mantém alternativas físicas sem promovê-las a arquitetura aprovada.

## Gaps

1. **Autoridade nativa do BMAD não tem aceite verificável.** A visão e PD-9 dizem que artefatos/estado BMAD continuam autoritativos quando adequados, mas FR-16/FR-17 não exigem que a Capability Matrix declare, por capability ou artefato, se a autoridade é BMAD-native, SB-native ou external/imported. Isso deixa a regra da SPEC (“reconciles and projects instead of duplicating blindly”) apenas narrativa.
2. **O registro de Override não exige explicitamente o estado/resultado da aprovação.** FR-6 pede “aprovação exigida”, mas o sucesso de CAP-3 requer `approval state`. Sem o resultado efetivo (`requested`, `accepted`, `rejected`, etc.), o aceite pode passar registrando somente que uma aprovação era necessária.
3. **A homologação foi promovida além do nível conceitual sem delimitação.** UJ-5 e FR-17 exigem Evidence de Contract Test para combinação BMAD/provider/executor, enquanto a SPEC permite modelar o Adapter conceitualmente antes do inventário completo e deixa os contract tests exatos como Open Question. Não é necessariamente escopo errado, mas falta distinguir o Contract Test mínimo do kernel de uma homologação operacional ampla.
4. **O critério de saída do PRD não testa diretamente o sinal de sucesso da SPEC.** §14 verifica qualidade documental, mas não declara como teste final que o PRD permite fatiar um primeiro epic implementável cobrindo Stage Contract, Readiness, Gate/Override, Assurance Core, reabertura e impacto nos três perfis.

## Conflitos ou tensões

- Não há contradição material com a SPEC.
- Há tensão de escopo entre o Adapter “conceitual” da assumption da SPEC e o fluxo completo de homologação de UJ-5/FR-17. Se o PRD mantiver este último, deve declarar qual evidência mínima cabe no MVP e o que permanece em OQ-8/OQ-9.
- FR-6 acrescenta validade/expiração do Override e FR-8 acrescenta revogação da autorização. São extensões compatíveis, mas não decisões confirmadas pela SPEC; devem permanecer explicitamente como decisão do PRD ou assumption, não ser atribuídas à fonte.

## Correções recomendadas

1. Acrescentar a FR-17 um aceite: cada Capability/Artifact declara sua autoridade (`BMAD-native`, `SB-native` ou `external/imported`) e a reconciliação não duplica nem substitui silenciosamente a fonte autoritativa.
2. Trocar em FR-6 “aprovação exigida” por requisitos separados de política aplicável e estado/resultado da aprovação, preservando quem decidiu e quando.
3. Delimitar em FR-17/UJ-5 um Contract Test conceitual mínimo dos seis operations e dos três perfis; manter inventário skill-by-skill e homologação ampla em OQ-8/OQ-9.
4. Complementar §14 com o sinal de sucesso da SPEC: capacidade de fatiar um primeiro epic implementável com as seis capacidades transversais e os três perfis, sem exigir arquitetura ou tickets nesta sessão.
