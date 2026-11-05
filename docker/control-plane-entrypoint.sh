#!/bin/sh
set -eu

# A file created by the short-lived verifier is required for every harness run.
# /proc/1 records this container start, so a stale named-volume file cannot be used.
while [ ! -f /tracer-runtime/tracer.env ] || [ ! /tracer-runtime/tracer.env -nt /proc/1 ]; do
  sleep 0.05
done

set -a
. /tracer-runtime/tracer.env
set +a
touch /tracer-runtime/control-plane.loaded
exec php -S 0.0.0.0:8000 -t public public/router.php
