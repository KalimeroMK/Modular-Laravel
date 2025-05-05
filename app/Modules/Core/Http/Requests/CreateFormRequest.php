<?php

namespace App\Modules\Core\Http\Requests;

abstract class CreateFormRequest extends FormRequest
{
    protected string $table = '';

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
    abstract public function rules(): array;
}
