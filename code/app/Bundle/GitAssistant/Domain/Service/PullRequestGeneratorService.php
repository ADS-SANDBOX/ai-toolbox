<?php

namespace App\Bundle\GitAssistant\Domain\Service;

interface PullRequestGeneratorService
{
    public function generateDescription(string $gitDiff, string $apiKey): array;
}
