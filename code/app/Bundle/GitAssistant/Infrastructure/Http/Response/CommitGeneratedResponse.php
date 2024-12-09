<?php

namespace App\Bundle\GitAssistant\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class CommitGeneratedResponse
{
    public function __construct(
        private string $commitMessage,
        private bool $cached,
        private string $expiresAt
    ) {}

    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'commit_message' => $this->commitMessage,
                'cached' => $this->cached,
                'expires_at' => $this->expiresAt,
            ],
            status: JsonResponse::HTTP_OK
        );
    }
}
