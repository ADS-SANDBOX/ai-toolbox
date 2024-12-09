<?php

namespace App\Bundle\GitAssistant\Domain\Exception\OpenAI;

use Exception;

final class ServiceUnavailableException extends Exception
{
    public function __construct()
    {
        parent::__construct('OpenAI service is currently unavailable');
    }

    public function status(): int
    {
        return 503;
    }
}
