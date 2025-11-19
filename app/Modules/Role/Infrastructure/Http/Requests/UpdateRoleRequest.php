<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Requests;

use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $role = $this->route('role');
        $roleId = $role instanceof Role ? $role->id : (is_numeric($role) ? $role : 'NULL');

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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $role = $this->route('role');
        if ($role && ! ($role instanceof Role) && ($route = $this->route()) !== null) {
            $route->setParameter('role', Role::findOrFail($role));
        }
    }
}
