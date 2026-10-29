---
tracker_id: "8"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/8"
tracker_status: backlog
id: 6
type: story
title: "Constrain direct-object access and internal service routes"
parent: epic-tenant-authority-foundation
covers: [CAP-15, CAP-16]
after: [4, 5]
hitl: false
risk: high
---

# Constrain direct-object access and internal service routes

## Description

Establishes the authorized short-lived presigned-object exception and rejects direct browser access to Laravel internals, queues, Runner Manager and runners.

## Acceptance Criteria

Verify: Only one authorized object/action can receive a short-lived direct URL, while attempted direct access to every internal service fails without disclosing the target.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-2 and AD-4
- _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, FR-1, FR-2 and NFR-2

## Notes

- Unknown: Object storage provider selection belongs to a later epic, so this story verifies the authorization contract through a bounded port.
