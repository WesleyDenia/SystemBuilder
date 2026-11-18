<?php

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\Ports\FixtureDelegationReplayGuard;

final class InMemoryFixtureDelegationReplayGuard implements FixtureDelegationReplayGuard
{
    private const PATH = '/tmp/systembuilder-tracer-replays.json';

    public function claim(string $jti, int $expiresAt): bool
    {
        $handle = fopen(self::PATH, 'c+');
        if ($handle === false || !flock($handle, LOCK_EX)) {
            return false;
        }

        try {
            $contents = stream_get_contents($handle);
            $claimed = $contents === false || $contents === '' ? [] : json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($claimed)) return false;
            $now = time();
            $claimed = array_filter($claimed, static fn (mixed $expiry): bool => is_int($expiry) && $expiry > $now);
            if (isset($claimed[$jti])) return false;
            $claimed[$jti] = $expiresAt;
            $encoded = json_encode($claimed, JSON_THROW_ON_ERROR);
            rewind($handle);
            if (ftruncate($handle, 0) === false || fwrite($handle, $encoded) !== strlen($encoded)) return false;
            return true;
        } catch (\JsonException) {
            return false;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
