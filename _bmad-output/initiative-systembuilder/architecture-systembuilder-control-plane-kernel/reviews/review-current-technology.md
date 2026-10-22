# Current-Technology Review — SystemBuilder SaaS Architecture Spine

**Reviewed:** `architecture-systembuilder-control-plane-kernel.md`, Stack and runtime-dependent rules  
**Date:** 2026-10-21  
**Method:** official vendor documentation/releases plus package-registry metadata where it is the distribution authority.

## Verdict

**Pass with required corrections.** The explicit application/database versions are real and current as of the review date; no stale or unsupported bound version was found. The stack is not yet reproducible or fully supportable because the Console omits its required Node/React runtime and the security-critical runner runtime is left as an unselected, unversioned alternative. Redis 8.10 is valid but is a short-support standard release, a questionable fit for a long-lived SaaS coordination store without an explicit upgrade policy.

## Verification matrix

| Binding in spine | Verification | Assessment |
| --- | --- | --- |
| Next.js 16.4.2 | The `v16.4.2` tag and npm package exist. Next.js 16 requires Node.js >=20.9 and TypeScript >=5.1; its App Router install instructions also require declared `react` and `react-dom` packages. [Next 16 announcement](https://nextjs.org/blog/next-16), [installation requirements](https://nextjs.org/docs/app/getting-started/installation) | Version is valid; companion runtime/dependency bindings are missing. |
| Laravel 13.38.0 | The `v13.38.0` framework tag/package exists. Laravel 13 requires PHP >=8.3. [Laravel deployment requirements](https://laravel.com/framework/docs/deployment) | Valid with PHP 8.5.12. |
| PHP 8.5.12 | Official PHP release/download pages identify 8.5.12 as the current security release on 2026-10-21. [PHP 8.5.12 announcement](https://www.php.net/releases/8_5_12.php) | Current and compatible. |
| MySQL 8.4.13 LTS | Oracle publishes 8.4.13 as the GA LTS release, released 2026-10-20. [MySQL 8.4.13 release notes](https://dev.mysql.com/doc/relnotes/mysql/8.4/en/news-8-4-13.html), [download page](https://dev.mysql.com/downloads/mysql/8.4.html) | Current and appropriate. |
| Redis Open Source 8.10.2 | The official release index publishes 8.10.2. Redis classifies 8.10 as a **Standard**, not Extended, release; standard releases receive security/critical fixes only until six months after the next minor release. [release index](https://download.redis.io/releases/), [version management](https://redis.io/docs/latest/operate/oss_and_stack/install/version-mgmt/) | Current, but its support model is a fit decision. |
| Hetzner Cloud + S3-compatible Object Storage | Hetzner documents S3-compatible Object Storage; it is available only in Nuremberg, Falkenstein, and Helsinki, and buckets expose regional public S3 endpoints. [Object Storage overview](https://docs.hetzner.com/storage/object-storage/overview/), [availability FAQ](https://docs.hetzner.com/storage/object-storage/faq/general/) | Product choice is valid; region/residency and mediation are correctly deferred/bound respectively. |
| Rootless OCI runtime: supported Docker or Podman, pinned later | Docker and Podman both support rootless operation, but rootless Docker has host prerequisites and can ignore cgroup resource flags when they are unmet. [Docker rootless mode](https://docs.docker.com/engine/security/rootless/), [rootless tips](https://docs.docker.com/engine/security/rootless/tips/), [Podman](https://podman.io/) | Not a reproducible technology binding yet. |

## Findings

### CT-1 — Console runtime and peer dependencies are absent from the stack

**Severity: High**

Next.js 16.4.2 has a Node.js >=20.9 engine requirement and peer dependencies on React/React DOM. The architecture pins Next.js but neither Node nor React/React DOM (or their lockfile/image-digest policy). A Console image could therefore be built on an EOL Node 20 line, an arbitrary compatible React major, or inconsistent developer/CI runtimes. Node 24 is the current LTS line through April 2028. [Next.js installation](https://nextjs.org/docs/app/getting-started/installation), [Node release schedule](https://nodejs.org/en/about/previous-releases), [Node 24 LTS lifecycle](https://nodejs.org/uk/blog/migrations/v22-to-v24).

**Correction:** bind `Node.js 24.x LTS` (patch pinned in the base-image/build lock), `react`/`react-dom` compatible with Next 16.4.2 and pinned by the package lock, plus the package-manager/lockfile enforcement. Do not bind an EOL Node 20 runtime simply because it satisfies Next's minimum.

### CT-2 — Runner runtime is an unresolved alternative despite being a security boundary

**Severity: High**

AD-7 relies on rootless containers and resource limits, yet the Stack permits either “supported Docker or Podman” and delegates version pinning to a future build manifest. The two have materially different daemon, networking, user-namespace, cgroup, and operational behavior. Docker documents prerequisites for rootless mode and notes that cgroup resource flags can be ignored if the host requirements are not met. An independently built Runner Manager cannot safely converge from this rule.

**Correction:** before the first Runner Manager implementation, select Docker Engine *or* Podman, bind an exact patched release/image digest and supported host OS/cgroup v2 baseline, and add a startup/CI conformance check for rootless UID mappings, cgroup enforcement, seccomp/AppArmor policy, disabled host loopback, and the absence of a host Docker socket. Keep stronger sandboxing (gVisor/Kata/microVM) deferred as the spine already does.

### CT-3 — Redis 8.10 is current but is not an extended-support release

**Severity: Medium**

Redis 8.10.2 exists and is current, but Redis calls 8.10 a Standard release. Its support window ends six months after the next minor release; by contrast, 8.2 is an Extended release with an advertised support horizon to September 2030. The architecture uses Redis only for cache/queue coordination, so it does not demonstrate a feature need that justifies the faster upgrade cadence.

**Correction:** either select the current patch on the Redis 8.2 Extended line for the MVP, or retain 8.10.2 with an explicit owner, automated patch/minor upgrade policy, compatibility test, and a review trigger on each Redis minor release. Also obtain legal review of the Redis Open Source licensing model before commercial distribution/hosting decisions; that is not established by the technical documentation.

### CT-4 — Hetzner Object Storage regional/residency constraint must become a pre-production deployment gate

**Severity: Low**

The product binding is technically valid, but the provider offers Object Storage only in three European locations and stores a bucket's data in its selected location. The spine defers the Hetzner region, while artifacts, uploads, exports, and backups are assigned to this service. This is acceptable for an architecture spine now, but it must not remain an untested infrastructure choice when customer/residency commitments are made.

**Correction:** retain the deferral, but add its concrete revisit condition: choose region/backup placement and validate data-residency, cross-region recovery, endpoint egress, and presigned-URL behavior before the first production tenant. No change is needed to the current public/private separation rule.

## No issue found

Laravel 13.38.0 + PHP 8.5.12, MySQL 8.4.13 LTS, and Next.js 16.4.2 are current compatible bindings. No technology-version contradiction was found between the framework, PHP, and database choices. The named object-storage service supports the S3-compatible adapter boundary, and its exposed S3 endpoint does not contradict AD-9 because the spine already requires Control Plane mediation or expiring presigned URLs.
