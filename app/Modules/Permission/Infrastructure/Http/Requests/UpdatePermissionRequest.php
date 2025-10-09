<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
        $permissionId = $this->route('permission')->id ?? 'NULL';

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:permissions,name,'.$permissionId],
            'guard_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.unique' => 'The permission name has already been taken.',
            'guard_name.string' => 'The guard name must be a string.',
        ];
    }
}
