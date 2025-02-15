<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'not_regex:/^\d+$/'],
            'description' => ['nullable', 'string', 'max:255','not_regex:/^\d+$/'],
            'is_completed' => ['required', 'boolean'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.not_regex' => 'the title must be a string',
            'title.string' => 'the title must be a string',
            'title.required' => 'the title is required',

            'description.not_regex' => 'the description must be a string',
            'description.string' => 'the description must be a string',
            'description.max' => 'the description must be less than 255 characters',

            'is_completed.required' => 'the is_completed is required',
            'is_completed.boolean' => 'the is_completed must be a boolean',

            'priority.in' => 'the priority must be low, medium, or high',
        ];
    }
}
