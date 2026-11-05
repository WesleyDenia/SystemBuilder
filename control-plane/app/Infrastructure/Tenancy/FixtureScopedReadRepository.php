<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\Ports\ScopedReadRepository;
use App\Domain\Tenancy\ScopedReadContext;
use App\Domain\Tenancy\ScopedReadFixture;

final class FixtureScopedReadRepository implements ScopedReadRepository
{
    public function read(ScopedReadContext $context): ?ScopedReadFixture
    {
        // This fixture is deliberately keyed server-side; neither HTTP adapter accepts scope values.
        if ($context->tenantId !== 'tenant-tracer' || $context->projectId !== 'project-tracer') {
            return null;
        }

        return new ScopedReadFixture('authorized scoped fixture');
    }
}
