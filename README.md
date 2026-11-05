# SystemBuilder tracer seed

This repository currently seeds the Console/BFF → Control Plane boundary. It is a bounded test harness, not production identity, persistence, authorization, or deployment design.

## Prerequisites

Only Docker Compose is required on the developer host. Node.js `24.22.1` and PHP `8.5.12` are pinned in the service images; neither runtime participates in host setup or verification.

## Canonical verification

```sh
sh scripts/verify-tracer.sh
```

The command owns a clean Compose lifecycle: it removes any previous tracer services and volumes before starting, runs `docker compose run --rm tracer-verifier`, and removes services and volumes again on success, failure, or interruption. It starts the Laravel Control Plane, Next.js BFF, PHPUnit component test service, and a short-lived verifier service. The verifier owns freshly generated fixture material, injects it only through a private Compose volume, and removes its own container on completion. It proves the BFF endpoint returns the v1 sanitized fixture, ignores browser-supplied scope selectors, mints fresh single-use delegations, and denies missing, malformed, expired, wrong-audience/action, replayed, and out-of-scope Laravel calls before the scoped query. The output deliberately contains neither delegation material nor the internal Control Plane address.

The browser endpoint is `GET /api/tracer`. It ignores browser-controlled scope input. `TRACER_CONTROL_PLANE_URL` and `TRACER_DELEGATION_KEY` are server-only variables: do not use `NEXT_PUBLIC_` variants. Laravel has no host port and is reachable only on the private Compose network; the BFF is the only application service bound to a host address. The internal Laravel adapter is for the local tracer harness only and is not a production API.

The versioned request, response, and error contracts are [contracts/tracer/v1/request.json](contracts/tracer/v1/request.json), [contracts/tracer/v1/scoped-read.json](contracts/tracer/v1/scoped-read.json), and [contracts/tracer/v1/errors.json](contracts/tracer/v1/errors.json). Delegation semantics are in [contracts/tracer/v1/delegation.md](contracts/tracer/v1/delegation.md), and the bounded threat model is [contracts/tracer/v1/STRIDE.md](contracts/tracer/v1/STRIDE.md).
