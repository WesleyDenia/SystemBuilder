#!/bin/sh
set -eu

# The browser process waits for verifier-owned, per-run server configuration.
while [ ! -f /tracer-runtime/tracer.env ] || [ ! /tracer-runtime/tracer.env -nt /proc/1 ]; do
  sleep 0.05
done

set -a
. /tracer-runtime/tracer.env
set +a
touch /tracer-runtime/console.loaded
exec npm run start:tracer -- --hostname 0.0.0.0 --port 3000
