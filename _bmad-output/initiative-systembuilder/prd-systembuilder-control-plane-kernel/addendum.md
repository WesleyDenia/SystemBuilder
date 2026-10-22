# Addendum — limites técnicos e decisões ainda abertas

Este addendum preserva profundidade que não cabe no PRD: mecanismos, alternativas e decisões a tomar antes das capacidades afetadas. A Architecture Spine é a interpretação aprovada de 0006; este ficheiro não a substitui nem converte OQs em escolhas.

## Limites arquiteturais já vinculativos

- Console/BFF é Next.js; Laravel é o Control Plane e autoridade final; Runner Manager é serviço do Execution Plane. Isto é uma fronteira aprovada, não uma sugestão de implementação.
- Dados governados estão em MySQL; Redis é coordenação/fila; Git é a fonte persistente de código; object storage S3 compatível guarda artefactos, exports e backups; workspaces de runner são descartáveis.
- O runner recebe somente checkout autorizado, BMAD read-only versionado, contexto mínimo e credenciais temporárias. Hosts de runner não coabitam com dados privados nem com a aplicação.
- O primeiro ambiente operacional é Hetzner com edge/app, dados privados e runners separados. A evolução horizontal e enterprise mantém os mesmos contratos de autoridade e isolamento.

## Mecanismos deliberadamente não escolhidos

- Runtime rootless, imagem de host e protocolo exacto Runner Manager; Docker/Podman rootless, gVisor, Kata e microVMs são alternativas, não decisão do PRD.
- Produto CI/CD, IaC, observabilidade, egress proxy, região, sizing, firewall detalhado, SLO/RTO/RPO.
- Provider, modelo, executor e método de credential onboarding iniciais; subscrição de consumo não implica uso de API.
- IdP/MFA/RBAC/ABAC, retenção, deleção/export, classificação/residência de dados e tenancy enterprise dedicada.
- Deployment Provider Adapter inicial e política pormenorizada de ambientes.

## Consequências a preservar no desenho

- A comunicação assíncrona precisa de dispatch durável e envelope canónico: correlação, versão, scope, deduplicação e ordenação. Implementação e valores de retry continuam OQ.
- A experiência não apresenta Evidence bruta nem segredo: BFF entrega modelo sanitizado. A classificação/redaction deve aplicar-se também a erros, nomes, URLs, tooltips e logs de cliente.
- O delivery de cliente requer secrets, approvals, rollout, health check e rollback próprios; nunca reutiliza por defeito pipeline ou credenciais de produção do SaaS.
- A taxonomia de Interaction Mode ainda tem conflito explícito entre UX (Guided/Advanced) e 0005 (Guided/Collaborative/Expert); não deve ser harmonizada por inferência.
