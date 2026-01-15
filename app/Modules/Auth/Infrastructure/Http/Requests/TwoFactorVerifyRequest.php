<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class TwoFactorVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required_without:recovery_code', 'string', 'size:6'],
            'recovery_code' => ['required_without:code', 'string', 'size:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'code.required' => 'The verification code is required.',
            'code.size' => 'The verification code must be exactly 6 digits.',
            'recovery_code.size' => 'The recovery code must be exactly 10 characters.',
        ];
    }
}
