<?php

namespace App\Bundle\User\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class ApiKeyUpdatedResponse
{
    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'message' => 'OpenAI API Key updated successfully',
            ],
            status: JsonResponse::HTTP_OK
        );
    }
}
