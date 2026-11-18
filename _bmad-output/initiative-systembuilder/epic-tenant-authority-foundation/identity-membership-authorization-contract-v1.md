---
contract: identity-membership-authorization
version: v1
status: approved
approved_on: 2026-11-18
owners: [Product, Architecture]
supersedes: null
---

# Identity, membership and authorization contract v1

## Decision

The MVP uses Laravel-local email/password authentication with a one-time code sent by email. It is a transitional control, not phishing-resistant MFA. TOTP or passkey MFA is mandatory before any external Tenant enters production.

## Authority and boundaries

- Laravel and MySQL are the authority for users, credential records, sessions, Tenant memberships, Project assignments and authorization decisions.
- The browser reaches only Console/BFF routes. It holds one opaque session cookie with `HttpOnly`, `Secure` and `SameSite=Lax` attributes; it never receives a Tenant, Project, role, internal URL or delegation credential.
- The BFF handles presentation and server-to-server transport only. It neither validates passwords or OTPs nor grants Tenant, Project, role or action scope.
- Every BFF route maps to a declared action. Laravel derives the principal from the session and reauthorizes membership, Tenant, Project and action before a scoped read or command. Browser inputs cannot select that scope.

## Admission, credentials and sessions

- A person joins only through a single-use Tenant-admin invitation. There is no self-registration.
- The invite expires after 72 hours. Password reset uses the same email challenge and invalidates all sessions.
- An email code is single-use, expires after 10 minutes, permits at most five verification attempts and at most three sends per hour per user.
- Passwords, email codes and session identifiers are persisted only as protected or hashed values. Responses, logs, Ledger, Artifact and Evidence never contain them.
- Sessions expire after 30 minutes of inactivity or 12 hours absolutely. Logout, password reset, membership removal or suspension, and role change revoke all affected sessions immediately.

## Membership and roles

The initial model is RBAC. Membership and Project assignment are explicit records, not claims trusted from the browser or BFF.

| Role | Initial authority |
| --- | --- |
| `tenant_admin` | Invites/removes members and manages Projects within its own Tenant. |
| `project_operator` | Works only in explicitly assigned Projects. |
| `reviewer` | Reads authorized Evidence/audit views and acts on a Gate only when the applicable Gate policy assigns that authority. |

Role membership is necessary but insufficient: Laravel validates the requested Tenant, Project and action at every governed boundary. No role may bypass a Gate or authorize a runner.

## BFF-to-Laravel delegation

For a declared BFF action, the BFF exchanges the opaque session with Laravel for a server-only delegation. The delegation is single-use, valid for no more than 60 seconds, and bound to its audience, action and immutable correlation. Laravel validates session/revocation when issuing it and again reauthorizes scope when consuming it. Any absent, expired, revoked, replayed, wrong-audience, wrong-action or malformed value fails closed before a scoped operation.

## Migration boundary

Application policy depends on an authenticated-principal, session and membership authorization port. Laravel-local credentials implement the initial identity adapter. A future external IdP replaces that adapter through a separately approved contract revision and explicit migration; it must not add a dual-acceptance fallback or change Laravel's final authorization rule.

## Required evidence

The implementation contract test suite must prove invitation-only admission; OTP expiry, single use and throttling; session expiry and every revocation path; altered Tenant/Project/action denial; cross-Tenant and cross-Project non-disclosure; BFF-only browser access; delegation expiry/audience/action/replay denial; and absence of credential material in browser output, logs and authorized Evidence.

## Residual risk

Email OTP depends on the security of the mailbox and is vulnerable to phishing. Product accepts this only for the internal MVP. The production onboarding Gate for every external Tenant requires TOTP or passkey MFA before authorization can be granted.
