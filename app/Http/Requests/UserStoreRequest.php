<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            "name" => ["required","string"],
            "email" => ["required","email","unique:users,email"],
            "username" => ["required","required","unique:users,username"],
            "password" => ["required","string","min:6 "],
            "role" => ['sometimes','string','exists:roles,name'],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'password' => Hash::make($this->password),
        ]);
    }

    protected function prepareForValidation(): void
    {
        // If no role provided, set default role to "user"
        if (! $this->has('role')) {
            $this->merge([
                'role' => 'user',
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The user\'s name is required.',
            'username.required' => 'The user\'s username is required.',
            'email.unique' => 'This email is already registered.',
        ];
    }
}
