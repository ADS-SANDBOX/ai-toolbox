<?php

namespace App\Bundle\GitAssistant\Domain\ValueObject;

final readonly class GitDiff
{
    public function __construct(private string $value) {}

    public function value(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        $content = trim($this->value);

        return $content === '' || $content === '0' || $content === 'null';
    }
}
