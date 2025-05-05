<?php

namespace App\Modules\Permission\Http\Requests;

use App\Modules\Core\Http\Requests\UpdateFormRequest;

class UpdatePermissionRequest extends UpdateFormRequest
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
            'id' => ['required', 'exists:permissions,id'],
            // Add other validation rules for update
        ];
    }
}
