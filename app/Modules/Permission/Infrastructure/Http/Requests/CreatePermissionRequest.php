<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class CreatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'guard_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    


    
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The permission name has already been taken.',
            'guard_name.string' => 'The guard name must be a string.',
        ];
    }
}
