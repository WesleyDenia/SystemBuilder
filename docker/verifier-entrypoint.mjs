import { randomBytes, randomUUID } from 'node:crypto';
import { existsSync, mkdirSync, renameSync, rmSync, writeFileSync } from 'node:fs';
import { spawnSync } from 'node:child_process';

const runtime = '/tracer-runtime';
const environmentPath = `${runtime}/tracer.env`;
const temporaryPath = `${environmentPath}.${process.pid}`;
const loadedMarkers = [`${runtime}/control-plane.loaded`, `${runtime}/console.loaded`];

try {
  mkdirSync(runtime, { recursive: true });
  // A retained named volume must never provide a previous run's configuration.
  rmSync(environmentPath, { force: true });
  rmSync(temporaryPath, { force: true });
  for (const marker of loadedMarkers) rmSync(marker, { force: true });

  const values = {
    APP_ENV: 'testing',
    APP_DEBUG: 'false',
    APP_KEY: `base64:${randomBytes(32).toString('base64')}`,
    TRACER_HARNESS_ENABLED: 'true',
    TRACER_DELEGATION_KEY: randomBytes(32).toString('base64url'),
    TRACER_VERIFICATION_KEY: randomBytes(32).toString('base64url'),
    TRACER_VERIFIER_CORRELATION: `bff-${randomUUID()}`,
    TRACER_CONTROL_PLANE_URL: 'http://control-plane:8000',
    TRACER_BFF_URL: 'http://console:3000',
  };

  // Only this short-lived service creates fixture material. The app services
  // acknowledge loading it before this file is removed.
  writeFileSync(temporaryPath, Object.entries(values).map(([key, value]) => `${key}=${value}`).join('\n').concat('\n'), { mode: 0o600 });
  renameSync(temporaryPath, environmentPath);
  await waitForLoadAcknowledgements();
  rmSync(environmentPath, { force: true });

  const result = spawnSync('node', ['/app/scripts/verify-tracer.mjs'], {
    env: { ...process.env, ...values },
    stdio: 'inherit',
  });
  process.exitCode = result.status ?? 1;
} catch (error) {
  process.stderr.write(`${error instanceof Error ? error.message : 'Verifier setup failed'}\n`);
  process.exitCode = 1;
} finally {
  // Keep secrets out of a retained named volume on success and every handled failure path.
  rmSync(temporaryPath, { force: true });
  rmSync(environmentPath, { force: true });
  for (const marker of loadedMarkers) rmSync(marker, { force: true });
}

async function waitForLoadAcknowledgements() {
  for (let attempt = 0; attempt < 200; attempt += 1) {
    if (loadedMarkers.every(existsSync)) return;
    await new Promise((resolveDelay) => setTimeout(resolveDelay, 50));
  }
  throw new Error('Tracer services did not acknowledge server configuration');
}
