import { createHmac, randomUUID } from 'node:crypto';
import { existsSync, readdirSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { spawnSync } from 'node:child_process';

const controlPlaneUrl = requiredEnvironment('TRACER_CONTROL_PLANE_URL');
const bffUrl = requiredEnvironment('TRACER_BFF_URL');
const delegationKey = requiredEnvironment('TRACER_DELEGATION_KEY');
const verificationKey = requiredEnvironment('TRACER_VERIFICATION_KEY');
const bffCorrelation = requiredEnvironment('TRACER_VERIFIER_CORRELATION');

try {
  runStructuralChecks();
  const bff = await waitFor(`${bffUrl}/api/tracer`, (response) => response.status === 200);
  assertSanitizedSuccess(await bff.text());

  const noisy = await fetch(`${bffUrl}/api/tracer?tenant_id=tenant-other&project_id=project-other&action=another-action`, {
    headers: { 'X-Tenant-Id': 'tenant-other', 'X-Project-Id': 'project-other', Cookie: 'tenant_id=tenant-other' },
  });
  assert(noisy.status === 200, 'BFF rejected safe ignored browser scope input');
  assertSanitizedSuccess(await noisy.text());

  const repeated = await fetch(`${bffUrl}/api/tracer`);
  assert(repeated.status === 200, 'BFF could not issue a fresh single-use delegation');
  assertSanitizedSuccess(await repeated.text());

  const rejected = await fetch(`${bffUrl}/api/tracer`, { method: 'POST', body: '{}' });
  assert(rejected.status === 405, 'BFF method allowlist did not reject POST');
  const oversized = await fetch(`${bffUrl}/api/tracer?x=${'x'.repeat(2050)}`);
  assert(oversized.status === 414, 'BFF URL size limit did not reject oversized input');
  await assertEvidence(bffCorrelation, 3);

  const denied = [
    ['missing', null],
    ['malformed', 'not-a-delegation'],
    ['expired', delegation({ exp: Math.floor(Date.now() / 1000) - 1 })],
    ['too-long-lived', delegation({ exp: Math.floor(Date.now() / 1000) + 125 })],
    ['wrong-audience', delegation({ aud: 'another-service' })],
    ['wrong-action', delegation({ action: 'another-action' })],
    ['out-of-scope-tenant', delegation({ tenant_id: 'tenant-other' })],
    ['out-of-scope-project', delegation({ project_id: 'project-other' })],
    ['invalid-jti', delegation({ jti: 'bad jti' })],
    ['extra-claim', delegation({ unexpected: 'value' })],
  ];
  for (const [name, value] of denied) {
    const correlation = `denied-${name}`;
    const response = await fetch(`${controlPlaneUrl}/api/internal/tracer/v1/scoped-read`, {
      headers: { 'X-Tracer-Correlation': correlation, ...(value === null ? {} : { 'X-Fixture-Delegation': value }) },
    });
    await assertDenied(response, name);
    await assertEvidence(correlation, 0);
  }

  const replayCorrelation = 'replay-check';
  const replay = delegation({ jti: 'replay-check-jti' });
  const first = await fetch(`${controlPlaneUrl}/api/internal/tracer/v1/scoped-read`, { headers: { 'X-Fixture-Delegation': replay, 'X-Tracer-Correlation': replayCorrelation } });
  assert(first.status === 200, 'Fresh direct fixture delegation was rejected');
  const second = await fetch(`${controlPlaneUrl}/api/internal/tracer/v1/scoped-read`, { headers: { 'X-Fixture-Delegation': replay, 'X-Tracer-Correlation': replayCorrelation } });
  await assertDenied(second, 'replayed');
  await assertEvidence(replayCorrelation, 1);

  assertNoLeaks([...staticAssets('/app/console'), ...staticAssets('/app/contracts')]);
  process.stdout.write('Tracer verified: BFF scoped reads, strict delegation, authorization, replay, and denial checks passed.\n');
} catch (error) {
  // Do not serialize response bodies, request headers, URLs, or environment values.
  process.stderr.write(`${error instanceof Error ? error.message : 'Tracer verification failed'}\n`);
  process.exitCode = 1;
}

function requiredEnvironment(name) {
  const value = process.env[name];
  if (!value) throw new Error(`Verifier configuration ${name} is unavailable`);
  return value;
}

function delegation(overrides = {}) {
  const claims = {
    iss: 'console-bff-fixture', aud: 'control-plane-tracer', exp: Math.floor(Date.now() / 1000) + 60,
    jti: randomUUID(), tenant_id: 'tenant-tracer', project_id: 'project-tracer', action: 'tracer.scoped-read', ...overrides,
  };
  const json = JSON.stringify(claims);
  const payload = Buffer.from(json).toString('base64url');
  return `${payload}.${createHmac('sha256', delegationKey).update(json).digest('base64url')}`;
}

async function waitFor(url, isReady) {
  for (let attempt = 0; attempt < 100; attempt += 1) {
    try {
      const response = await fetch(url, { signal: AbortSignal.timeout(2_000) });
      if (isReady(response)) return response;
    } catch { /* retry until bounded timeout */ }
    await new Promise((resolveDelay) => setTimeout(resolveDelay, 100));
  }
  throw new Error('Tracer service did not become ready within 10 seconds');
}

async function assertEvidence(correlation, expected) {
  const response = await fetch(`${controlPlaneUrl}/api/internal/tracer/v1/invocations?correlation=${encodeURIComponent(correlation)}`, { headers: { 'X-Tracer-Verification-Key': verificationKey } });
  const body = await response.json();
  assert(response.status === 200 && body.invocations === expected, `Unexpected invocation evidence: expected ${expected}, got ${String(body.invocations)}`);
}

async function assertDenied(response, name) {
  const body = await response.text();
  assert(response.status === 401, `Direct ${name} delegation was not denied`);
  assert(body === '{"error":"tracer_access_denied"}' && !body.includes('authorized scoped fixture'), `Direct ${name} denial leaked fixture data`);
}

function assertSanitizedSuccess(body) {
  assert(body === '{"version":"tracer/v1","fixture":{"label":"authorized scoped fixture"}}', 'BFF response was not the v1 sanitized contract');
  assert(!body.includes(delegationKey) && !body.includes(controlPlaneUrl), 'BFF response exposed internal tracer material');
}

function assertNoLeaks(values) {
  for (const value of values) {
    assert(!value.includes(delegationKey), 'Tracer static material exposed the delegation key');
    assert(!value.includes(verificationKey), 'Tracer static material exposed the verification key');
    assert(!value.includes(controlPlaneUrl), 'Tracer static material exposed the Control Plane address');
  }
}

function staticAssets(directory) {
  if (!existsSync(directory)) return [];
  const values = [];
  for (const entry of readdirSync(directory, { withFileTypes: true })) {
    const path = resolve(directory, entry.name);
    if (entry.isDirectory()) values.push(...staticAssets(path));
    else values.push(readFileSync(path, 'utf8'));
  }
  return values;
}

function runStructuralChecks() {
  const result = spawnSync(process.execPath, ['/app/scripts/check-tracer-structure.mjs'], { encoding: 'utf8' });
  if (result.status !== 0) throw new Error(result.stderr.trim() || 'Tracer structural checks failed');
}

function assert(condition, message) {
  if (!condition) throw new Error(message);
}
