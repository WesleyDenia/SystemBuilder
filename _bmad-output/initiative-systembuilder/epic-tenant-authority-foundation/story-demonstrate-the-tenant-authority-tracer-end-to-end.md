---
tracker_id: "11"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/11"
tracker_status: backlog
id: 9
type: story
title: "Demonstrate the tenant-authority tracer end to end"
parent: epic-tenant-authority-foundation
covers: [CAP-15, CAP-16]
after: [8]
hitl: false
risk: high
---

# Demonstrate the tenant-authority tracer end to end

## Description

Runs the final foundation tracer through the Console/BFF and Laravel scope boundary using the canonical commands and authorized test fixtures.

## Acceptance Criteria

Verify: An independent reviewer can run the documented tracer and observe authorized scoped access plus all required denial and safe-surface evidence.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md, Done when
- _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, SM-1 and NFR-4

## Notes

- Unknown: The final fixture follows the identity contract chosen in story 2 and must not simulate an unapproved external provider.
