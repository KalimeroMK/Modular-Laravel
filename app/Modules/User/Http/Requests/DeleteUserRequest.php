<?php

namespace App\Modules\User\Http\Requests;

use App\Modules\Core\Http\Requests\DeleteFormRequest;

class DeleteUserRequest extends DeleteFormRequest
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
            'id' => ['required', 'exists:users,id'],
        ];
    }
}
