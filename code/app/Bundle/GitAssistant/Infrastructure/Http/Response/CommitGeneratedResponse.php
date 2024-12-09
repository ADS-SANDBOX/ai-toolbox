<?php

namespace App\Bundle\GitAssistant\Infrastructure\Http\Response;

use Illuminate\Http\JsonResponse;

final readonly class CommitGeneratedResponse
{
    public function __construct(
        private string $commitMessage
    ) {}

    public function toResponse(): JsonResponse
    {
        return response()->json(
            data: [
                'commit_message' => $this->commitMessage,
            ],
            status: JsonResponse::HTTP_OK
        );
    }
}
