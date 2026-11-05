FROM node:24.22.1-bookworm-slim

WORKDIR /app
COPY contracts ./contracts
COPY console ./console
COPY control-plane ./control-plane
COPY scripts/check-tracer-structure.mjs scripts/verify-tracer.mjs ./scripts/
COPY docker/verifier-entrypoint.mjs /usr/local/bin/verifier-entrypoint.mjs

ENTRYPOINT ["node", "/usr/local/bin/verifier-entrypoint.mjs"]
