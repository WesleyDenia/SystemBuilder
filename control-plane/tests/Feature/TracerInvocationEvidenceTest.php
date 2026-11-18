<?php

namespace Tests\Feature;

use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;
use Tests\TestCase;

final class TracerInvocationEvidenceTest extends TestCase
{
    public function test_authorized_verifier_reads_invocation_evidence_through_the_http_adapter(): void
    {
        config(['tracer.verification_key' => 'verification-key-for-test']);
        app(ScopedReadInvocationEvidence::class)->record('runtime-evidence');

        $this->withHeader('X-Tracer-Verification-Key', 'verification-key-for-test')
            ->getJson('/api/internal/tracer/v1/invocations?correlation=runtime-evidence')
            ->assertOk()
            ->assertExactJson(['invocations' => 1]);
    }

    public function test_missing_or_invalid_verification_key_cannot_read_invocation_evidence(): void
    {
        config(['tracer.verification_key' => 'verification-key-for-test']);

        foreach ([null, 'wrong-verification-key'] as $key) {
            $request = $this;
            if ($key !== null) {
                $request = $this->withHeader('X-Tracer-Verification-Key', $key);
            }

            $request->getJson('/api/internal/tracer/v1/invocations?correlation=denied-evidence')
                ->assertForbidden()
                ->assertExactJson(['error' => 'tracer_access_denied']);
        }
    }

    public function test_invalid_correlation_returns_a_sanitized_request_error(): void
    {
        config(['tracer.verification_key' => 'verification-key-for-test']);

        $this->withHeader('X-Tracer-Verification-Key', 'verification-key-for-test')
            ->getJson('/api/internal/tracer/v1/invocations?correlation=contains%20space')
            ->assertUnprocessable()
            ->assertExactJson(['error' => 'tracer_request_rejected']);
    }
}
