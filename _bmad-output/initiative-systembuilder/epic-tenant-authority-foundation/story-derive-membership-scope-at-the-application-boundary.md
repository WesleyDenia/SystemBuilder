---
tracker_id: "5"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/5"
tracker_status: backlog
id: 3
type: story
title: "Derive membership scope at the application boundary"
parent: epic-tenant-authority-foundation
covers: [CAP-15]
after: [2]
hitl: false
risk: high
---

# Derive membership scope at the application boundary

## Description

Implements the approved membership context and Laravel application authorization boundary so browser-provided identifiers cannot grant Tenant, Project or action scope.

## Acceptance Criteria

Verify: Authenticated requests with altered Tenant, Project, header or query identifiers are denied without changing the server-derived authorized scope.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, FR-1 and NFR-4
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-3 and AD-4

## Notes

- Unknown: The concrete session-claim mapping depends on the approved OQ-1 contract.
