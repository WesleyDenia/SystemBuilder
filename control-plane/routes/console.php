<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-tracer', function (): void {
    $this->comment('SystemBuilder Control Plane tracer seed');
});
