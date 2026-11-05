<?php

namespace App\Domain\Tenancy;

final readonly class ScopedReadContext
{
    public function __construct(
        public string $tenantId,
        public string $projectId,
        public string $action,
    ) {
    }
}
