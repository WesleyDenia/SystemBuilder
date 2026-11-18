---
tracker_id: "4"
remote: "https://github.com/WesleyDenia/SystemBuilder/issues/4"
tracker_status: in-progress
id: 2
type: story
title: "Record the initial identity and membership authorization contract"
parent: epic-tenant-authority-foundation
covers: [CAP-15]
after: [1]
hitl: true
risk: high
refined: true
---

# Record the initial identity and membership authorization contract

## Description

Records the OQ-1 decision for identity, MFA and role or attribute authorization while preserving Laravel as final authorizer.

## Acceptance Criteria

1. Given the approved OQ-1 contract, when a maintainer reads it, then it defines the local identity adapter, email challenge limits, invitation-only admission, sessions, memberships, roles, revocation and Laravel reauthorization.
2. Given a browser or BFF request, when it reaches a scoped Laravel operation, then the contract requires server-derived membership, Tenant, Project and action authorization and rejects untrusted or invalid delegation before the operation.
3. Given an external Tenant production onboarding, when stronger MFA is unavailable, then the contract makes that onboarding ineligible rather than treating email OTP as an equivalent control.
4. Given a future external IdP migration, when it is proposed, then the contract requires a separately approved revision and forbids an implicit dual-acceptance fallback or a change to Laravel final authorization.

## References

- parent — _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/epic-tenant-authority-foundation.md
- _bmad-output/initiative-systembuilder/prd-systembuilder-control-plane-kernel/prd-systembuilder-control-plane-kernel.md, OQ-1
- _bmad-output/initiative-systembuilder/architecture-systembuilder-control-plane-kernel/architecture-systembuilder-control-plane-kernel.md, AD-3 and AD-4
- _bmad-output/initiative-systembuilder/epic-tenant-authority-foundation/identity-membership-authorization-contract-v1.md

## Notes

- Decision (2026-11-18): Product and Architecture approved `identity-membership-authorization-contract-v1.md`. Laravel-local email/password plus email OTP is limited to the internal MVP; external Tenant production onboarding requires TOTP or passkey MFA.
