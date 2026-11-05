import { createHmac, randomUUID } from 'node:crypto';

const required = ['TRACER_CONTROL_PLANE_URL', 'TRACER_DELEGATION_KEY', 'TRACER_VERIFIER_CORRELATION'] as const;

export type TracerConfiguration = {
  controlPlaneUrl: string;
  delegationKey: string;
  correlation: string;
};

export function tracerConfiguration(): TracerConfiguration {
  for (const name of required) {
    if (!process.env[name] || process.env[name]?.startsWith('generated-')) {
      throw new Error('Tracer server configuration is unavailable');
    }
  }

  const controlPlaneUrl = process.env.TRACER_CONTROL_PLANE_URL!;
  if (!/^http:\/\/(?:127\.0\.0\.1|control-plane):\d+$/.test(controlPlaneUrl)) {
    throw new Error('Tracer Control Plane configuration is invalid');
  }

  const correlation = process.env.TRACER_VERIFIER_CORRELATION!;
  if (!/^[A-Za-z0-9-]{1,128}$/.test(correlation)) {
    throw new Error('Tracer verifier correlation is invalid');
  }
  return { controlPlaneUrl, delegationKey: process.env.TRACER_DELEGATION_KEY!, correlation };
}

export function freshFixtureDelegation(delegationKey: string): string {
  const payload = JSON.stringify({
    iss: 'console-bff-fixture',
    aud: 'control-plane-tracer',
    exp: Math.floor(Date.now() / 1000) + 60,
    jti: randomUUID(),
    tenant_id: 'tenant-tracer',
    project_id: 'project-tracer',
    action: 'tracer.scoped-read',
  });
  const encoded = Buffer.from(payload).toString('base64url');
  const signature = createHmac('sha256', delegationKey).update(Buffer.from(encoded, 'base64url')).digest('base64url');
  return `${encoded}.${signature}`;
}
