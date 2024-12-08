<?php

namespace App\Bundle\User\Application\UseCase\UpdateApiKey;

final readonly class UpdateApiKeyDTO
{
    public function __construct(
        private string $userId,
        private string $apiKey
    ) {}

    public function userId(): string
    {
        return $this->userId;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }
}
