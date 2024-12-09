<?php

namespace App\Bundle\GitAssistant\Application\UseCase\GeneratePullRequest;

use App\Bundle\GitAssistant\Domain\ValueObject\GitDiff;

final readonly class GeneratePullRequestDTO
{
    public function __construct(
        private GitDiff $gitDiff,
        private string $userId
    ) {}

    public function gitDiff(): GitDiff
    {
        return $this->gitDiff;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
