<?php

namespace App\Modules\Role\Http\Requests;

use App\Modules\Core\Http\Requests\CreateFormRequest;

class CreateRoleRequest extends CreateFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|integer|exists:permissions,id',
        ];
    }
}
