<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'guard_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The role name has already been taken.',
            'guard_name.string' => 'The guard name must be a string.',
        ];
    }
}
