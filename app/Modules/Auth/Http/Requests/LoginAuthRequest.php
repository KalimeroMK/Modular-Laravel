<?php

namespace App\Modules\Auth\Http\Requests;

use App\Modules\Core\Http\Requests\CreateFormRequest;

class LoginAuthRequest extends CreateFormRequest
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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
