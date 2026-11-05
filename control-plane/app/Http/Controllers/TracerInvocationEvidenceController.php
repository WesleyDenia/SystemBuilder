<?php

namespace App\Http\Controllers;

use App\Application\Tenancy\Ports\ScopedReadInvocationEvidence;
use App\Http\Requests\TracerVerificationRequest;
use Illuminate\Http\JsonResponse;

final class TracerInvocationEvidenceController
{
    public function __invoke(TracerVerificationRequest $request, ScopedReadInvocationEvidence $evidence): JsonResponse
    {
        return response()->json(['invocations' => $evidence->count((string) $request->validated('correlation'))]);
    }
}
