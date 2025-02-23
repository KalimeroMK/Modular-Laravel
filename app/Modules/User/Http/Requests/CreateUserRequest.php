<?php

namespace App\Modules\User\Http\Requests;

use App\Modules\Core\Http\Requests\CreateFormRequest;

class CreateUserRequest extends CreateFormRequest
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
            // Add validation rules here
        ];
    }
}
