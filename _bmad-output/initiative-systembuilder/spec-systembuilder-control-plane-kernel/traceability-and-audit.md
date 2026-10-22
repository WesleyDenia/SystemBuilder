# Traceability and Audit

## Ledger

O Ledger é append-oriented. Criação/mudança de Stage Revision, Readiness, Gate, Override, Decision, autorização, Execution, reconciliação, Baseline e impacto produzem eventos correlacionados. Cada evento identifica Tenant, Project, actor/origin, tempo, tipo, entidade e causa quando aplicável; correção cria evento/versão nova, nunca reescreve história.

Famílias versionadas: lifecycle, execução, Gate/aprovação, Override/Exceção Formal, Artifact, Evidence, Finding, impacto, Adapter/Contract Test e violação de fronteira. Eventos não aplicáveis não são fabricados. Falha de persistência impede a transição governada correspondente.

## Links e consultas

Links iniciais: `derives_from`, `satisfies`, `implements`, `verifies`, `blocks`, `supersedes`, `impacts`, `invalidates`, `accepts_risk_for`. Tipo desconhecido exige extensão versionada.

Partindo de Requirement, Decision, Baseline, Artifact, Evidence, Gate ou Finding, é possível recuperar entidades e eventos relacionados, quem autorizou, o que executou, Input/versão, Evidence e risco residual; as consultas incluem disposição downstream e divergência BMAD/SystemBuilder com a respetiva reconciliação. Isto não depende de logs brutos do runner.

## Propriedades obrigatórias

- Leitura/mutação cross-Tenant ou cross-Project por ID manipulado falha e é auditada.
- Secrets não entram em browser, ledger, stdout/stderr persistido, prompt archive, Artifact ou Evidence.
- Logs de runner são correlacionados à Execution; storage do host não é fonte de verdade.
- Runner falhado/destruído não apaga eventos, Artifact ou Evidence já aceites.
