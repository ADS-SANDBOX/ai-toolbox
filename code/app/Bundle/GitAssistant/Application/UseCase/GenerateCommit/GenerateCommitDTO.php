<?php

namespace App\Bundle\GitAssistant\Application\UseCase\GenerateCommit;

final readonly class GenerateCommitDTO
{
    public function __construct(
        private string $gitDiff,
        private string $userId
    ) {}

    public function gitDiff(): string
    {
        return $this->gitDiff;
    }

    public function isEmptyDiff(): bool
    {
        $diffContent = trim($this->gitDiff);

        return empty($diffContent) || $diffContent === 'null';
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
