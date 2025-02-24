<?php

namespace App\Modules\Permission\Http\Requests;

use App\Modules\Core\Http\Requests\ShowFormRequest;

class ShowPermissionRequest extends ShowFormRequest
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
            'id' => ['required', 'exists:permissions,id'],
        ];
    }
}
