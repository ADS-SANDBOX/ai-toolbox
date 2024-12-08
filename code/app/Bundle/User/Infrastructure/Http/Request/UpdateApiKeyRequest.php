<?php

namespace App\Bundle\User\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateApiKeyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'api_key' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'api_key.required' => 'OpenAI API Key is required',
        ];
    }
}
