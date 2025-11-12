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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure route model binding is resolved before validation
        $permission = $this->route('permission');
        if ($permission && !($permission instanceof \App\Modules\Permission\Infrastructure\Models\Permission)) {
            // If it's not a model instance, try to resolve it
            $permission = \App\Modules\Permission\Infrastructure\Models\Permission::findOrFail($permission);
            $this->route()->setParameter('permission', $permission);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $permission = $this->route('permission');
        
        // Handle both model instance and ID
        if ($permission instanceof \App\Modules\Permission\Infrastructure\Models\Permission) {
            $permissionId = $permission->id;
        } elseif (is_numeric($permission)) {
            $permissionId = $permission;
        } else {
            $permissionId = 'NULL';
        }

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
