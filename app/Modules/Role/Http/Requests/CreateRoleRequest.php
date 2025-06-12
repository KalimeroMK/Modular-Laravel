<?php

namespace App\Modules\Role\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest {
    public function rules(): array {
        return ['name' => 'required|string|max:255'];
    }
}