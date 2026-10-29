---
tracker_id: "3"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/3"
tracker_status: backlog
id: 1
type: story
title: "Seed the Control Plane and Console/BFF tracer"
parent: epic-tenant-authority-foundation
covers: [CAP-15, CAP-16]
after: []
hitl: false
risk: high
---

# Seed the Control Plane and Console/BFF tracer

## Description

Creates the Laravel, Next.js BFF, shared contract and verification-command skeletons with a test-only scoped request crossing the BFF to Laravel.

## Acceptance Criteria

Verify: A documented canonical command proves a test membership context reaches a Laravel scoped read through the BFF and the browser has no direct Laravel route.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, Stack and Structural Seed
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-1–AD-3

## Notes

- Unknown: The repository has no application manifests or canonical commands, so the structural seed must make both explicit.
