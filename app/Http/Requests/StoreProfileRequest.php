<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d'],
            'bio' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048',],
        ];
    }

    function messages()
    {
        return [
            'phone.required' => 'the phone is required',
            'phone.string' => 'the phone must be a string',
            'phone.max' => 'the phone must be less than 255 characters',


            'address.required' => 'the address is required',
            'address.string' => 'the address must be a string',
            'address.max' => 'the address must be less than 255 characters',


            'date_of_birth.required' => 'the date of birth is required',
            'date_of_birth.date' => 'the date of birth must be a date',
            'date_of_birth.date_format' => 'the date of birth must be in the format YYYY-MM-DD like: 2000-02-01',


            'bio.required' => 'the bio is required',
            'bio.string' => 'the bio must be a string',
            'bio.max' => 'the bio must be less than 255 characters',

            'image.image' => 'the image must be an image',

            'user_id.required' => 'the user id is required',
            'user_id.exists' => 'the user id does not exist',
            'user_id.unique' => 'this user already has a profile'

        ];
    }
}
