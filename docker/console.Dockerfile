FROM node:24.22.1-bookworm-slim

WORKDIR /app
COPY package.json package-lock.json ./
COPY console/package.json ./console/package.json
RUN npm ci --include=dev
COPY console ./console
RUN npm run build --workspace @systembuilder/console
COPY docker/console-entrypoint.sh /usr/local/bin/console-entrypoint
RUN chmod 0555 /usr/local/bin/console-entrypoint

WORKDIR /app/console
ENTRYPOINT ["/usr/local/bin/console-entrypoint"]
