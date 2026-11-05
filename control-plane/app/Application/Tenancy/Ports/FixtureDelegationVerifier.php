<?php

namespace App\Application\Tenancy\Ports;

use App\Domain\Tenancy\ScopedReadContext;

interface FixtureDelegationVerifier
{
    /** Returns only a server-derived context or null; never returns raw delegation material. */
    public function verify(string $delegation): ?ScopedReadContext;
}
