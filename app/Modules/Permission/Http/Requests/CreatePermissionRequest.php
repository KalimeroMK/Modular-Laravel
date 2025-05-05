<?php

namespace App\Modules\Permission\Http\Requests;

use App\Modules\Core\Http\Requests\CreateFormRequest;

class CreatePermissionRequest extends CreateFormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:permissions,name',
        ];
    }
}
