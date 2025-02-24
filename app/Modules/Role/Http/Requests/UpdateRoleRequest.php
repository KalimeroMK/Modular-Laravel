<?php

namespace App\Modules\Role\Http\Requests;

use App\Modules\Core\Http\Requests\UpdateFormRequest;

class UpdateRoleRequest extends UpdateFormRequest
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
            'id' => ['required', 'exists:roles,id'],
            'name' => ['required'],
            ];
    }
}
