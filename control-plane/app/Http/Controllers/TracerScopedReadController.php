<?php

namespace App\Http\Controllers;

use App\Application\Tenancy\ReadScopedTracerFixture;
use App\Http\Requests\TracerDelegationRequest;
use Illuminate\Http\JsonResponse;

final class TracerScopedReadController
{
    public function __invoke(TracerDelegationRequest $request, ReadScopedTracerFixture $query): JsonResponse
    {
        $correlation = $request->correlation();
        if ($correlation === null) {
            return response()->json(['error' => 'tracer_access_denied'], 401);
        }

        $fixture = $query->handle($request->delegation(), $correlation);
        if ($fixture === null) {
            return response()->json(['error' => 'tracer_access_denied'], 401);
        }

        return response()->json([
            'version' => 'tracer/v1',
            'fixture' => ['label' => $fixture->label],
        ]);
    }
}
