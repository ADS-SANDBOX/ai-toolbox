<?php

namespace App\Bundle\User\Domain\Exception;

use Exception;

final class UserOpenaiApiKeyMissingException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct(
            sprintf('User with id %s does not have an OpenAI API key configured', $id)
        );
    }
}
