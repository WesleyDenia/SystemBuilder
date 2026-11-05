<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\Ports\MembershipAuthorizer;
use App\Domain\Tenancy\ScopedReadContext;

final class InMemoryFixtureMembershipAuthorizer implements MembershipAuthorizer
{
    public function authorizes(ScopedReadContext $context): bool
    {
        return $context->tenantId === 'tenant-tracer'
            && $context->projectId === 'project-tracer'
            && $context->action === 'tracer.scoped-read';
    }
}
