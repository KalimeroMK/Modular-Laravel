<?php

namespace App\Modules\Core\Http\Requests;

abstract class UpdateFormRequest extends FormRequest
{
    protected string $table = '';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return array_merge($this->request->all(), [
            'id' => $this->route()->parameter('id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;
}
