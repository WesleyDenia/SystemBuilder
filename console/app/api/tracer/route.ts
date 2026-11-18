import { NextResponse } from 'next/server';
import { readTracerFixture } from '@/bff/tracer-client';

export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  if (process.env.TRACER_HARNESS_ENABLED !== 'true') {
    return NextResponse.json({ error: 'not_found' }, { status: 404 });
  }

  const contentLength = Number(request.headers.get('content-length') ?? '0');
  if (request.body !== null || !Number.isSafeInteger(contentLength) || contentLength < 0 || contentLength > 0) {
    return NextResponse.json({ error: 'tracer_request_rejected' }, { status: 400 });
  }
  if (request.url.length > 2048) {
    return NextResponse.json({ error: 'tracer_request_rejected' }, { status: 414 });
  }

  // Query, headers, cookies, and body never select scope; server configuration derives it.
  try {
    const fixture = await readTracerFixture();
    if (fixture === null) {
      return NextResponse.json({ error: 'tracer_unavailable' }, { status: 502 });
    }
    return NextResponse.json(fixture);
  } catch {
    return NextResponse.json({ error: 'tracer_unavailable' }, { status: 502 });
  }
}
