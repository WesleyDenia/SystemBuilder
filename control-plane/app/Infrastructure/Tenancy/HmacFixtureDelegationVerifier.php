<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\Ports\FixtureDelegationVerifier;
use App\Application\Tenancy\Ports\FixtureDelegationReplayGuard;
use App\Domain\Tenancy\ScopedReadContext;

final readonly class HmacFixtureDelegationVerifier implements FixtureDelegationVerifier
{
    public function __construct(
        private string $key,
        private string $issuer,
        private string $audience,
        private string $action,
        private int $maxTtlSeconds,
        private FixtureDelegationReplayGuard $replays,
    ) {
    }

    public function verify(string $delegation): ?ScopedReadContext
    {
        if ($this->key === '' || strlen($delegation) > 4096) return null;
        $segments = explode('.', $delegation);
        if (count($segments) !== 2) return null;

        [$encodedPayload, $encodedSignature] = $segments;
        $payload = $this->base64UrlDecode($encodedPayload);
        $signature = $this->base64UrlDecode($encodedSignature);
        if ($payload === null || $signature === null) return null;

        $expected = hash_hmac('sha256', $payload, $this->key, true);
        if (!hash_equals($expected, $signature)) return null;

        try {
            $claims = json_decode($payload, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (!is_array($claims) || array_keys($claims) !== ['iss', 'aud', 'exp', 'jti', 'tenant_id', 'project_id', 'action']) return null;
        if ($claims['iss'] !== $this->issuer || $claims['aud'] !== $this->audience || $claims['action'] !== $this->action) return null;
        if (!is_int($claims['exp']) || $claims['exp'] <= time() || $claims['exp'] > time() + $this->maxTtlSeconds) return null;
        if (!is_string($claims['jti']) || preg_match('/^[A-Za-z0-9-]{1,64}$/', $claims['jti']) !== 1) return null;
        if (!is_string($claims['tenant_id']) || !is_string($claims['project_id'])) return null;
        if ($claims['tenant_id'] === '' || $claims['project_id'] === '') return null;
        if (!$this->replays->claim($claims['jti'], $claims['exp'])) return null;

        return new ScopedReadContext($claims['tenant_id'], $claims['project_id'], $claims['action']);
    }

    private function base64UrlDecode(string $value): ?string
    {
        if ($value === '' || preg_match('/^[A-Za-z0-9_-]+$/', $value) !== 1) return null;
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', (4 - strlen($value) % 4) % 4), true);
        return $decoded === false ? null : $decoded;
    }
}
