<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ['required','string','unique:roles,name'],
            "guard_name" => ['in:api'],
            "permissions" => ['array'], //optional array of permissions names
            "permissions.*" => ['string','exists:permissions,name'] //each must exist in DB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The role name is required.',
            'name.string'   => 'The role name must be a valid string.',
            'name.unique'   => 'This role name already exists. Please choose another one.',

            'permissions.array' => 'Permissions must be provided as an array.',
            'permissions.*.string' => 'Each permission must be a valid string.',
            'permissions.*.exists' => 'One or more of the provided permissions are invalid.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'guard_name' => 'api',
        ]);
    }


}
