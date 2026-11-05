<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TracerVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $key = (string) config('tracer.verification_key', '');
        return $key !== '' && hash_equals($key, (string) $this->header('X-Tracer-Verification-Key', ''));
    }

    public function rules(): array
    {
        return ['correlation' => ['required', 'string', 'max:128', 'regex:/^[A-Za-z0-9-]+$/']];
    }
}
