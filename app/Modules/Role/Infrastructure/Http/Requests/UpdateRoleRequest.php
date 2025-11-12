<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $role = $this->route('role');
        if ($role && !($role instanceof \App\Modules\Role\Infrastructure\Models\Role)) {
            // If it's not a model instance, try to resolve it
            $role = \App\Modules\Role\Infrastructure\Models\Role::findOrFail($role);
            $this->route()->setParameter('role', $role);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $role = $this->route('role');
        
        // Handle both model instance and ID
        if ($role instanceof \App\Modules\Role\Infrastructure\Models\Role) {
            $roleId = $role->id;
        } elseif (is_numeric($role)) {
            $roleId = $role;
        } else {
            $roleId = 'NULL';
        }

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:roles,name,'.$roleId],
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
            'name.unique' => 'The role name has already been taken.',
            'guard_name.string' => 'The guard name must be a string.',
        ];
    }
}
