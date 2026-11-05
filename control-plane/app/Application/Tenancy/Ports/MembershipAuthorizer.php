<?php

namespace App\Application\Tenancy\Ports;

use App\Domain\Tenancy\ScopedReadContext;

interface MembershipAuthorizer
{
    public function authorizes(ScopedReadContext $context): bool;
}
