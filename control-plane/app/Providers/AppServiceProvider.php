<?php

namespace App\Providers;

use App\Application\Tenancy\Ports\FixtureDelegationVerifier;
use App\Application\Tenancy\Ports\FixtureDelegationReplayGuard;
use App\Application\Tenancy\Ports\MembershipAuthorizer;
use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;
use App\Application\Tenancy\Ports\ScopedReadRepository;
use App\Infrastructure\Tenancy\InMemoryFixtureDelegationReplayGuard;
use App\Infrastructure\Tenancy\InMemoryFixtureMembershipAuthorizer;
use App\Infrastructure\Tenancy\InMemoryScopedReadInvocationEvidence;
use App\Infrastructure\Tenancy\FixtureScopedReadRepository;
use App\Infrastructure\Tenancy\HmacFixtureDelegationVerifier;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FixtureDelegationVerifier::class, fn (): HmacFixtureDelegationVerifier => new HmacFixtureDelegationVerifier(
            (string) config('tracer.delegation_key', ''),
            (string) config('tracer.issuer'),
            (string) config('tracer.audience'),
            (string) config('tracer.action'),
            (int) config('tracer.max_ttl_seconds'),
            $this->app->make(FixtureDelegationReplayGuard::class),
        ));
        $this->app->singleton(FixtureDelegationReplayGuard::class, InMemoryFixtureDelegationReplayGuard::class);
        $this->app->singleton(MembershipAuthorizer::class, InMemoryFixtureMembershipAuthorizer::class);
        $this->app->singleton(ScopedReadInvocationEvidence::class, InMemoryScopedReadInvocationEvidence::class);
        $this->app->singleton(ScopedReadRepository::class, FixtureScopedReadRepository::class);
    }
}
