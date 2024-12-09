<?php

namespace App\Bundle\GitAssistant\Domain\Exception\OpenAI;
use Exception;
final class InvalidApiKeyException extends Exception
{
    public function __construct()
    {
        parent::__construct('The provided OpenAI API key is invalid');
    }

    public function status(): int
    {
        return 401;
    }
}
