<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class TracerVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $key = (string) config('tracer.verification_key', '');
        return $key !== '' && hash_equals($key, (string) $this->header('X-Tracer-Verification-Key', ''));
    }

    public function rules(): array
    {
        return ['correlation' => ['bail', 'required', 'string', 'max:128', 'regex:/^[A-Za-z0-9-]+$/']];
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json(['error' => 'tracer_access_denied'], 403));
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['error' => 'tracer_request_rejected'], 422));
    }
}
