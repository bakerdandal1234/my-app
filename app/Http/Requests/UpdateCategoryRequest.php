<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:255'],
        ];
    }

    Public function messages()
    {
        return [
            'name.string' => 'the name must be a string',
            'name.max' => 'the name must be less than 255 characters',
            'description.string' => 'the description must be a string',
            'description.max' => 'the description must be less than 255 characters',
        ];
    }
}
