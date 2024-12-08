<?php

namespace App\Bundle\User\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class UserRegisteredResponse
{
    public function __construct(
        private string $token
    ) {}

    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'token' => $this->token,
                'message' => 'User registered successfully',
            ],
            status: JsonResponse::HTTP_CREATED
        );
    }
}
