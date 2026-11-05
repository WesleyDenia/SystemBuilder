<?php

namespace App\Application\Tenancy;

use App\Application\Tenancy\Ports\FixtureDelegationVerifier;
use App\Application\Tenancy\Ports\MembershipAuthorizer;
use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;
use App\Application\Tenancy\Ports\ScopedReadRepository;
use App\Domain\Tenancy\ScopedReadFixture;

final readonly class ReadScopedTracerFixture
{
    public function __construct(
        private FixtureDelegationVerifier $delegations,
        private MembershipAuthorizer $memberships,
        private ScopedReadRepository $fixtures,
        private ScopedReadInvocationEvidence $evidence,
    ) {
    }

    /**
     * The repository is reached only after the infrastructure adapter derives a valid context.
     */
    public function handle(string $delegation, string $correlation): ?ScopedReadFixture
    {
        $context = $this->delegations->verify($delegation);
        if ($context === null) {
            return null;
        }
        if (!$this->memberships->authorizes($context)) {
            return null;
        }

        $this->evidence->record($correlation);
        return $this->fixtures->read($context);
    }
}
