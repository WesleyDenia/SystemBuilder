import { freshFixtureDelegation, tracerConfiguration } from '@/server/tracer-config';

export type TracerResponse = {
  version: 'tracer/v1';
  fixture: { label: 'authorized scoped fixture' };
};

export async function readTracerFixture(): Promise<TracerResponse | null> {
  const { controlPlaneUrl, delegationKey, correlation } = tracerConfiguration();
  const response = await fetch(`${controlPlaneUrl}/api/internal/tracer/v1/scoped-read`, {
    headers: {
      Accept: 'application/json',
      'X-Fixture-Delegation': freshFixtureDelegation(delegationKey),
      'X-Tracer-Correlation': correlation,
    },
    cache: 'no-store',
    signal: AbortSignal.timeout(2_000),
  });

  if (!response.ok) return null;
  const data: unknown = await response.json();
  if (!isTracerResponse(data)) return null;
  return data;
}

function isTracerResponse(data: unknown): data is TracerResponse {
  if (typeof data !== 'object' || data === null) return false;
  const value = data as Record<string, unknown>;
  if (Object.keys(value).length !== 2 || !Object.hasOwn(value, 'version') || !Object.hasOwn(value, 'fixture')) return false;
  if (typeof value.fixture !== 'object' || value.fixture === null) return false;
  const fixture = value.fixture as Record<string, unknown>;
  return value.version === 'tracer/v1'
    && Object.keys(fixture).length === 1
    && fixture.label === 'authorized scoped fixture';
}
