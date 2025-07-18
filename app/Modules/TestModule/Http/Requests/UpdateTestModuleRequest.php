<?php

namespace App\Modules\TestModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestModuleRequest extends FormRequest
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
            //             'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
