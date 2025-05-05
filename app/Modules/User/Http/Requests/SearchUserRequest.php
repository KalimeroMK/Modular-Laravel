<?php

namespace App\Modules\User\Http\Requests;

use App\Modules\Core\Http\Requests\SearchFormRequest;

class SearchUserRequest extends SearchFormRequest
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
            'query' => ['nullable', 'string', 'max:255'],
        ];
    }
}
