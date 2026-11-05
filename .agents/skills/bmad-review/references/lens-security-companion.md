# Security Review Companion

This is a mandatory companion to the `adversarial` lens, not a replacement for it. It runs after the adversarial findings are available so that the security reviewer can consider them as context, while independently assessing the original review content.

1. Load and follow the complete installed **`bmad-review-security`** skill. Its instructions, safeguards, templates, and evidence rules govern this pass.
2. Give it the same staged content path, content classification, review scope, and `also_consider` areas used by the parent review. Provide adversarial findings as supplemental context only; do not let them suppress an independent security finding.
3. Preserve the security skill's authority boundaries: review only, do not implement a fix; do not disclose secrets or PII; only execute already-available local static-analysis tools; report unavailable checks as verification gaps.
4. Return the report using `bmad-review-security`'s required format: 🔴 Alto Risco, 🟡 Médio Risco, 🟢 Baixo Risco, coverage/limitations, and an explicit decision. Do not emit the parent review's canonical JSON shape for this companion.

If `bmad-review-security` cannot be loaded, emit a security report with decision **BLOQUEADO**, identifying the missing skill as an essential verification gap. Do not silently skip the companion review.
