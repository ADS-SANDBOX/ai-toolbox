<?php

namespace App\Bundle\User\Domain\Exception;

use InvalidArgumentException;

final class EmptyApiKeyException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('API key cannot be empty');
    }
}
