<?php

namespace App\Bundle\GitAssistant\Domain\Service;

interface CommitGeneratorService
{
    public function generateMessage(string $gitDiff, string $apiKey): array;
}
