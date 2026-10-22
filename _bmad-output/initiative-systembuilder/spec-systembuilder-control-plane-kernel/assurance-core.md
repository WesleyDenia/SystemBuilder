# Assurance Core

## Registo mínimo

Cada Execution relevante produz Assurance Record com `tenant_id`, `project_id`, `stage_id`, `stage_revision`, `workflow_id`, `run_id`/identidade transversal, início/fim ou event time, actor/origin, engine e versão, configuração/política, refs de Input/output, Gate, resultado e `evidence_refs`. Decision humana, Finding, risco residual, guardrail version, base commit/revision e usage/cost são condicionais; risco residual é obrigatório para `CONCERNS` aceite e Override de `FAIL`.

Evidence é referenciada por identidade e integridade, não duplicada. Campo mínimo em falta impede que a Execution suporte Gate de fechamento. Maior profundidade por Stage Profile/risco pode adicionar campos, nunca remover os aplicáveis.

## Profundidade por perfil

| Perfil | Profundidade adicional |
| --- | --- |
| Ideation/Brainstorm | Provenance, hipóteses, Capability/método, outputs, decisões e concerns. |
| PRD | Provenance de Requirement, contradições tratadas, suposições, aprovações, âncoras e impacto downstream. |
| Coding/Story | Readiness da Story, branch/base, tests, code/security review, diffs/commits, Findings e risco residual. |

O Assurance acomoda hallucination, falta de rastreabilidade, integração, infraestrutura, permissões excessivas, prompt injection, supply chain, specification gaming, complacência humana, desalinhamento de negócio e Finding de segurança. Não é binário: começa antes do código e cresce com risco, criticidade e contexto.
