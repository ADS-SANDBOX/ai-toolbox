<?php

namespace App\Bundle\User\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class UserLoggedResponse
{
    public function __construct(
        private string $token
    ) {}

    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'token' => $this->token,
                'message' => 'User logged in successfully',
            ],
            status: JsonResponse::HTTP_OK
        );
    }
}
