<?php

return [
    'delegation_key' => env('TRACER_DELEGATION_KEY'),
    'issuer' => 'console-bff-fixture',
    'audience' => 'control-plane-tracer',
    'action' => 'tracer.scoped-read',
    'max_ttl_seconds' => 120,
    'verification_key' => env('TRACER_VERIFICATION_KEY'),
];
