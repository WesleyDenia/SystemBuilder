<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TracerDelegationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function delegation(): string
    {
        return (string) $this->header('X-Fixture-Delegation', '');
    }

    public function correlation(): ?string
    {
        $correlation = $this->header('X-Tracer-Correlation');
        if (!is_string($correlation) || preg_match('/^[A-Za-z0-9-]{1,128}$/', $correlation) !== 1) {
            return null;
        }

        return $correlation;
    }
}
