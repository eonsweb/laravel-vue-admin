<?php

namespace App\Http\Requests;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["sometimes","string"],
            "email" => ["sometimes","email",Rule::unique(User::class)->ignore($this->user()->id)],
            "username" => ["sometimes","required",Rule::unique(User::class)->ignore($this->user()->id)],
            "password" => ["sometimes","string","min:6"],
            "role" => ['sometimes','string','exists:roles,name'],
        ];
    }

    protected function passedValidation()
    {
        if ($this->filled('password')) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        } else {
            $this->request->remove('password'); // don't send empty value to update
        }
    }


}
