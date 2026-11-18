<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;

final class InMemoryScopedReadInvocationEvidence implements ScopedReadInvocationEvidence
{
    private const PATH = '/tmp/systembuilder-tracer-invocations.json';

    public function record(string $correlation): void
    {
        $this->withCounts(function (array &$counts) use ($correlation): void {
            $counts[$correlation] = ($counts[$correlation] ?? 0) + 1;
        });
    }

    public function count(string $correlation): int
    {
        $count = 0;
        $this->withCounts(function (array $counts) use ($correlation, &$count): void {
            $count = $counts[$correlation] ?? 0;
        });
        return $count;
    }

    /** @param callable(array<string, int>&): void $operation */
    private function withCounts(callable $operation): void
    {
        $handle = fopen(self::PATH, 'c+');
        if ($handle === false || !flock($handle, LOCK_EX)) {
            throw new \RuntimeException('Fixture invocation evidence is unavailable');
        }

        try {
            $contents = stream_get_contents($handle);
            $counts = $contents === false || $contents === '' ? [] : json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($counts)) {
                throw new \RuntimeException('Fixture invocation evidence is invalid');
            }
            $operation($counts);
            $encoded = json_encode($counts, JSON_THROW_ON_ERROR);
            rewind($handle);
            if (ftruncate($handle, 0) === false || fwrite($handle, $encoded) !== strlen($encoded)) {
                throw new \RuntimeException('Fixture invocation evidence is unavailable');
            }
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
