<?php

namespace App\Application\Tenancy\Ports;

interface ScopedReadInvocationEvidence
{
    public function record(string $correlation): void;

    public function count(string $correlation): int;
}
