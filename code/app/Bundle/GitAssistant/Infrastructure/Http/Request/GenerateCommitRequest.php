<?php

namespace App\Bundle\GitAssistant\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class GenerateCommitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'git_diff' => 'required|string|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'git_diff.required' => 'Git diff content is required',
            'git_diff.string' => 'Git diff must be a string',
            'git_diff.min' => 'Git diff cannot be empty',
        ];
    }
}
