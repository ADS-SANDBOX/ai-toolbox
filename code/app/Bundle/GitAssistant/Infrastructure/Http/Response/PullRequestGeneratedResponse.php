<?php

namespace App\Bundle\GitAssistant\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class PullRequestGeneratedResponse
{
    public function __construct(
        private string $description,
        private bool $cached,
        private string $expiresAt
    ) {}

    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'pull_request_description' => $this->description,
                'cached' => $this->cached,
                'expires_at' => $this->expiresAt,
            ],
            status: JsonResponse::HTTP_OK
        );
    }
}
