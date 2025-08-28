<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLinkRequest extends FormRequest
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
            'url' => ['required', 'url', 'max:2048'],
            'custom_alias' => ['nullable', 'string', 'max:20', 'unique:links,short_code'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'title' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'url.required' => 'Please enter a valid URL.',
            'url.url' => 'Please enter a valid URL format.',
            'url.max' => 'URL is too long (maximum 2048 characters).',
            'custom_alias.regex' => 'Custom alias can only contain letters, numbers, hyphens, and underscores.',
            'custom_alias.unique' => 'This custom alias is already taken. Please choose another one.',
            'password.min' => 'Password must be at least 4 characters long.',
            'expires_at.after' => 'Expiration date must be in the future.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
