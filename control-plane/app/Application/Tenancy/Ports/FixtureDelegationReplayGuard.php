<?php

namespace App\Application\Tenancy\Ports;

interface FixtureDelegationReplayGuard
{
    /** Claims the ID until delegation expiry; false means the ID was already consumed. */
    public function claim(string $jti, int $expiresAt): bool;
}
