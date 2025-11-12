<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure route model binding is resolved before validation
        $user = $this->route('user');
        if ($user && !($user instanceof \App\Modules\User\Infrastructure\Models\User)) {
            // If it's not a model instance, try to resolve it
            $user = \App\Modules\User\Infrastructure\Models\User::findOrFail($user);
            $this->route()->setParameter('user', $user);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        
        // Handle both model instance and ID
        if ($user instanceof \App\Modules\User\Infrastructure\Models\User) {
            $userId = $user->id;
        } elseif (is_numeric($user)) {
            $userId = $user;
        } else {
            $userId = 'NULL';
        }

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'password' => ['sometimes', 'string', 'min:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
