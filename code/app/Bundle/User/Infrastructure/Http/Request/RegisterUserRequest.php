<?php

namespace App\Bundle\User\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot be longer than 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.max' => 'Email cannot be longer than 255 characters',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters long',
        ];
    }
}
