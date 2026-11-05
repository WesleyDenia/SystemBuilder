import { readFileSync } from 'node:fs';

const files = {
  controller: 'control-plane/app/Http/Controllers/TracerScopedReadController.php',
  useCase: 'control-plane/app/Application/Tenancy/ReadScopedTracerFixture.php',
  bff: 'console/app/api/tracer/route.ts',
  client: 'console/bff/tracer-client.ts',
};
const source = Object.fromEntries(Object.entries(files).map(([name, path]) => [name, readFileSync(path, 'utf8')]));

assert(!/Infrastructure\\Tenancy|FixtureScopedReadRepository|HmacFixtureDelegationVerifier/.test(source.controller), 'HTTP controller must not depend on tenancy infrastructure');
assert(/MembershipAuthorizer/.test(source.useCase) && /ScopedReadInvocationEvidence/.test(source.useCase), 'Application use case must own membership and evidence seams');
assert(!/tenant[_A-Z-]*id|project[_A-Z-]*id|membership|authoriz/i.test(source.bff), 'BFF route must not make tenancy or membership decisions');
assert(!/tenant[_A-Z-]*id|project[_A-Z-]*id|membership|authoriz/i.test(source.client.replace(/authorized scoped fixture/g, '')), 'BFF client must not make tenancy or membership decisions');
assert(!/NEXT_PUBLIC_(TRACER_CONTROL_PLANE_URL|TRACER_DELEGATION_KEY)/.test(`${source.bff}\n${source.client}`), 'Browser code must not expose tracer server configuration');
process.stdout.write('Tracer structural checks passed.\n');

function assert(condition, message) {
  if (!condition) throw new Error(message);
}
