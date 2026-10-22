# Reconciliação: Architecture Reference

Fonte reconciliada: `spec-systembuilder-control-plane-kernel/architecture-reference.md`  
Alvos: `prd-systembuilder-control-plane-kernel.md` e `addendum.md`

## Cobertura forte

- A fronteira Control Plane / Execution Plane / BMAD Adapter foi incorporada em PD-8, PD-9, FR-8, FR-9, FR-16 e NFR-5. O PRD preserva a regra essencial: o Control Plane governa e não executa código/agentes de cliente em seu processo.
- A cadeia `Tenant → Project → Stage/Workflow → Execution → Artifact/Evidence` está no escopo e em FR-1; Decision, Gate, Finding e Traceability Link aparecem no glossário, FR-5 e FR-10–FR-12.
- As seis operações conceituais do BMAD Adapter foram preservadas literalmente em FR-16 e a proibição de acoplamento a path, filename, enum, prompt ou skill interna está verificável.
- Capability Matrix, homologação, limitações, versão, Evidence de Contract Test e ausência de fallback silencioso estão cobertos por FR-17 e NFR-7/NFR-12.
- A autorização escopada, temporary secrets, contexto mínimo, isolamento por Project, revogação e devolução de eventos/Artifact/Evidence estão cobertos por FR-8, FR-9 e NFR-4–NFR-8.
- Os mecanismos físicos sugeridos historicamente foram corretamente preservados no addendum como candidatos, sem serem promovidos a arquitetura aprovada.

## Gaps

1. **Capability Matrix perde dimensões semânticas da fonte.** FR-17 exige combinação, Capability, limitações, versão e teste, mas não torna obrigatório representar: autoridade de Artifact, natureza do suporte de Readiness (`native`, `adapter-derived`, `SB-only`), origem/situação da Evidence e features relevantes do provider.
2. **Escopo de todas as entidades governadas não está inequívoco em FR-1.** O enunciado enumera Stage Revision, Workflow, Execution, Artifact e Evidence, mas omite Decision, Gate, Finding, Traceability Link, Capability Matrix e credenciais/policies. O critério “toda entidade criada” é mais amplo que o enunciado, criando ambiguidade de isolamento.
3. **Envelope mínimo recebido pelo runner está apenas parcialmente verificável.** Branch/base commit, assets BMAD read-only e workspace único por Execution aparecem na fonte/addendum ou em OQ-10, mas não como propriedades observáveis exigidas quando aplicáveis a Coding/Story.
4. **Separação conceitual entre Provider Credential e Execution Adapter não foi elevada a requisito.** NFR-6 e NFR-7 cobrem segurança e abstração, mas não impedem um desenho que acople credencial de Tenant ao executor/provider de maneira que dificulte troca ou revogação independente.

## Conflitos

- A referência cita Laravel no fluxo de criação de Execution; o PRD exclui seleção de stack e o addendum trata mecanismos concretos como históricos. A leitura vigente deve manter “Control Plane” e descartar Laravel como decisão.
- A cadeia de rastreabilidade da fonte alcança Release; o PRD limita o MVP aos três Stage Profile e o addendum registra explicitamente que entrega/deploy não serão implementados. Isso é um corte de escopo intencional, não perda do modelo extensível.
- A fonte atribui “encrypted credentials” ao Control Plane; o PRD exige as propriedades de segurança, mas deixa armazenamento e secret broker em OQ-3/OQ-6. Não se deve converter a formulação da fonte em escolha física sem a arquitetura.

## Correções recomendadas

- Expandir FR-17 para exigir, como dimensões conceituais mínimas, workflow coverage, Artifact authority, readiness support, Evidence support, execution adapter e provider features relevantes.
- Alargar FR-1/NFR-4 para enumerar todas as entidades governadas e exigir Tenant/Project scoping onde aplicável, sem decidir o schema físico.
- Adicionar a FR-8/FR-20 critérios observáveis para workspace exclusivo, branch/base commit e assets BMAD read-only quando o executor usar filesystem/repositório.
- Acrescentar um invariante conceitual: credential ownership/lifecycle é independente de Execution Adapter e provider selection; modelo físico continua em OQ-6.

