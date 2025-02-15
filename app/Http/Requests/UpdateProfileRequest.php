<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'phone' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
            'date_of_birth' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'bio' => ['sometimes', 'string', 'max:50'],
            'user_id' => ['sometimes', 'exists:users,id', 'unique:profiles,user_id'],
        ];
    }

    function messages()
    {
        return [
            'phone.string' => 'the phone must be a string',
            'address.string' => 'the address must be a string',
            'date_of_birth.date_format' => 'the date of birth must be in the format YYYY-MM-DD like: 2000-02-01',
            'bio.string' => 'the bio must be a string',
            'bio.max' => 'the bio must be less than 50 characters',
            'user_id.exists' => 'the user id does not exist',
            'user_id.unique' => 'this user already has a profile'
        ];
    }
}

