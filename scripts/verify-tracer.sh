#!/bin/sh
set -eu

cleanup() {
  docker compose down --volumes --remove-orphans >/dev/null 2>&1 || true
}

cleanup
trap cleanup EXIT HUP INT TERM
docker compose run --rm tracer-verifier
