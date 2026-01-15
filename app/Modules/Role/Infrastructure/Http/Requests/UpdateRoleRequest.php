<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

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
        $id = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:roles,name,'.$id],
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
            'name.string' => 'The name must be a string.',
            'name.unique' => 'The role name has already been taken.',
            'guard_name.string' => 'The guard name must be a string.',
        ];
    }
}
