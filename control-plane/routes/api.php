<?php

use App\Http\Controllers\TracerInvocationEvidenceController;
use App\Http\Controllers\TracerScopedReadController;
use Illuminate\Support\Facades\Route;

if (app()->environment('testing')) {
    Route::get('/internal/tracer/v1/scoped-read', TracerScopedReadController::class)
        ->name('internal.tracer.scoped-read');
    Route::get('/internal/tracer/v1/invocations', TracerInvocationEvidenceController::class)
        ->name('internal.tracer.invocations');
}
