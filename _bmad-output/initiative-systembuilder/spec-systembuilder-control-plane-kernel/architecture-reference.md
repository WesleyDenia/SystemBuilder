# Referência de fronteira e integração

## Fronteiras de produto

| Domínio | Responsabilidade |
| --- | --- |
| Control Plane | Identidade, Tenant/Project, Stage/Workflow, políticas, Gate, autorização, estado projetado, Evidence index, risco, impacto, auditabilidade e homologação. |
| Execution Plane | Execução isolada de agentes, comandos BMAD, testes e reviews com token, contexto e secrets temporários e escopados. |
| BMAD Adapter | Camada anticorrupção versionada entre semântica SB e versão/capabilities homologadas de BMAD. |

O Control Plane não executa código/agentes no seu processo e o Execution Plane não tem acesso direto global ou a outro Project. Credencial e Execution Adapter são conceitos independentes; escolha de provider não muda regra de negócio.

## Adapter e Capability Matrix

Operações estáveis: `inspect`, `validate_entry`, `invoke`, `collect`, `reconcile`, `project_state`. Toda chamada regista Adapter e BMAD alvo. Mudança incompatível impede homologação até ajuste e Contract Test.

A Matrix versionada declara combinação homologada, Capability, limitações, versão, Evidence de Contract Test, autoridade de Artifact/estado (`BMAD-native`, `SB-native`, `external/imported`), suporte de Readiness/Evidence, Execution Adapter e feature relevante de provider. O Contract Test mínimo cobre as seis operações e lifecycle comum dos três Stage Profile; Capability obrigatória ausente/incompatível impede autorização sem fallback silencioso.

## Entidades conceituais

`Tenant → Project → Stage Revision/Workflow → Execution → Artifact/Evidence`, cruzadas por Decision, Gate, Finding e Traceability Link. A forma física, IDs, cardinalidades, mensagens e storage permanecem questões de arquitetura, não decisões desta SPEC.
