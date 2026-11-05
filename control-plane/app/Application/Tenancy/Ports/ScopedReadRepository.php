<?php

namespace App\Application\Tenancy\Ports;

use App\Domain\Tenancy\ScopedReadContext;
use App\Domain\Tenancy\ScopedReadFixture;

interface ScopedReadRepository
{
    public function read(ScopedReadContext $context): ?ScopedReadFixture;
}
