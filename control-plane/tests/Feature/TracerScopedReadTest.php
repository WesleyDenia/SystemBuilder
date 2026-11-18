<?php

namespace Tests\Feature;

use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class TracerScopedReadTest extends TestCase
{
    public function test_valid_server_delegation_returns_only_the_sanitized_fixture_and_invokes_once(): void
    {
        $response = $this->withHeaders([
            'X-Fixture-Delegation' => self::delegation(),
            'X-Tracer-Correlation' => 'authorized-read',
        ])->getJson('/api/internal/tracer/v1/scoped-read');

        $response->assertOk()->assertExactJson([
            'version' => 'tracer/v1',
            'fixture' => ['label' => 'authorized scoped fixture'],
        ]);
        $this->assertInvocationCount('authorized-read', 1);
    }

    #[DataProvider('deniedDelegations')]
    public function test_denied_delegation_never_invokes_the_scoped_read(?string $delegation): void
    {
        $request = $this->withHeader('X-Tracer-Correlation', 'denied-read');
        if ($delegation !== null) $request = $request->withHeader('X-Fixture-Delegation', $delegation);

        $response = $request->getJson('/api/internal/tracer/v1/scoped-read');
        $response->assertUnauthorized()->assertExactJson(['error' => 'tracer_access_denied']);
        $this->assertStringNotContainsString('authorized scoped fixture', $response->getContent());
        $this->assertInvocationCount('denied-read', 0);
    }

    public static function deniedDelegations(): array
    {
        return [
            'missing' => [null],
            'malformed' => ['not-a-delegation'],
            'expired' => [self::delegation(['exp' => time() - 1])],
            'too long lived' => [self::delegation(['exp' => time() + 125])],
            'wrong issuer' => [self::delegation(['iss' => 'another-issuer'])],
            'wrong audience' => [self::delegation(['aud' => 'another-service'])],
            'wrong action' => [self::delegation(['action' => 'another-action'])],
            'out of scope tenant' => [self::delegation(['tenant_id' => 'tenant-other'])],
            'out of scope project' => [self::delegation(['project_id' => 'project-other'])],
            'invalid jti' => [self::delegation(['jti' => 'contains space'])],
            'extra claim' => [self::delegation(['unexpected' => 'value'])],
            'invalid signature' => [self::invalidSignature(self::delegation())],
        ];
    }

    public function test_replayed_delegation_is_denied_before_a_second_scoped_read(): void
    {
        $delegation = self::delegation(['jti' => 'one-use-delegation']);
        $this->withHeaders(['X-Fixture-Delegation' => $delegation, 'X-Tracer-Correlation' => 'replayed-read'])
            ->getJson('/api/internal/tracer/v1/scoped-read')->assertOk();
        $this->withHeaders(['X-Fixture-Delegation' => $delegation, 'X-Tracer-Correlation' => 'replayed-read'])
            ->getJson('/api/internal/tracer/v1/scoped-read')->assertUnauthorized();
        $this->assertInvocationCount('replayed-read', 1);
    }

    public function test_missing_or_malformed_correlation_is_denied_before_the_scoped_read(): void
    {
        foreach ([null, 'contains space'] as $correlation) {
            $request = $this->withHeader('X-Fixture-Delegation', self::delegation());
            if ($correlation !== null) {
                $request = $request->withHeader('X-Tracer-Correlation', $correlation);
            }

            $request->getJson('/api/internal/tracer/v1/scoped-read')
                ->assertUnauthorized()
                ->assertExactJson(['error' => 'tracer_access_denied']);
        }
        $this->assertInvocationCount('uncorrelated', 0);
    }

    private function assertInvocationCount(string $correlation, int $expected): void
    {
        $this->assertSame($expected, app(ScopedReadInvocationEvidence::class)->count($correlation));
    }

    private static function delegation(array $overrides = []): string
    {
        $claims = array_merge([
            'iss' => 'console-bff-fixture',
            'aud' => 'control-plane-tracer',
            'exp' => time() + 60,
            'jti' => 'jti-'.bin2hex(random_bytes(8)),
            'tenant_id' => 'tenant-tracer',
            'project_id' => 'project-tracer',
            'action' => 'tracer.scoped-read',
        ], $overrides);
        $json = json_encode($claims, JSON_THROW_ON_ERROR);
        $payload = self::base64Url($json);
        $signature = self::base64Url(hash_hmac('sha256', $json, 'test-harness-key-not-a-production-secret', true));

        return $payload.'.'.$signature;
    }

    private static function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function invalidSignature(string $delegation): string
    {
        [$payload, $signature] = explode('.', $delegation, 2);
        return $payload.'.'.($signature[0] === 'A' ? 'B' : 'A').substr($signature, 1);
    }
}
