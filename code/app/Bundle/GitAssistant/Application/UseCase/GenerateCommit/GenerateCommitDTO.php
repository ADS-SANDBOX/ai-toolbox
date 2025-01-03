<?php

namespace App\Bundle\GitAssistant\Application\UseCase\GenerateCommit;

use App\Bundle\GitAssistant\Domain\ValueObject\GitDiff;

final readonly class GenerateCommitDTO
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
