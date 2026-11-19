---
name: systembuilder-plan-review-gate
description: "Gate a SystemBuilder story implementation plan with a mandatory adversarial review and an independent security review before it can move to ready-for-development. Use immediately after a story plan is written or materially changed."
---

# SystemBuilder Plan Review Gate

Use this gate immediately after the plan is saved in `draft` and before it is presented for approval, set to `ready-for-dev`, or handed to implementation.

## Inputs and scope

1. Resolve the supplied story or plan to exactly one `*-plan.md` file. If no plan exists, stop: planning must create it first.
2. Read the complete plan, its frontmatter context files, the parent story and epic where present, and `AGENTS.md`.
3. State the exact plan revision under review. A materially edited plan requires a new gate run; do not reuse a report for a different revision.

## Mandatory review chain

1. Invoke `bmad-review` with the plan as content and the explicit directive `skill:bmad-review lenses=adversarial`.
2. The adversarial lens must run first. Its mandatory dependent `security-review` must then run using the complete `bmad-review-security` skill and receive the adversarial findings.
3. The security reviewer must be independent of the plan author. If independent review cannot be demonstrated, record the failure as a governance blocker; do not claim the gate passed.
4. Do not replace this chain with two parallel reviews. An optional independent preliminary security pass may run in parallel, but it does not satisfy this gate because the required security pass evaluates the adversarial findings.

## Evidence and decision

Write an immutable report beside the plan, named `<plan-stem>-review-<first-12-characters-of-plan-sha256>.md`. It must identify the plan revision, reviewer independence, adversarial findings, the complete security report (including the required STRIDE coverage and decision), and the resulting gate decision.

- **Blocked:** any unresolved High Risk finding, missing STRIDE coverage for a material risk flow, missing independent security reviewer, or unavailable essential evidence. Leave the plan unapproved and state the next action.
- **Needs human risk decision:** Medium/Low findings requiring acceptance, or plan/architecture changes proposed by security review. Do not alter the plan; present the report for human decision.
- **Ready for approval:** no blockers and every finding is either resolved in the exact reviewed plan or explicitly presented for the human to accept. The gate does not itself approve the plan.

The review skills are read-only. Preserve prior reports for traceability and never overwrite a report for another plan revision. Do not implement review recommendations in the same gate run; corrections require a new plan revision and another complete gate.
