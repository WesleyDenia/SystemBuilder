<?php

namespace App\Domain\Tenancy;

final readonly class ScopedReadFixture
{
    public function __construct(public string $label)
    {
    }
}
