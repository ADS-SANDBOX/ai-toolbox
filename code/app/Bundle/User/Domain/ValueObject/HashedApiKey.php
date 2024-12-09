<?php

namespace App\Bundle\User\Domain\ValueObject;

use App\Bundle\User\Domain\Exception\EmptyApiKeyException;
use Illuminate\Support\Facades\Crypt;

final readonly class HashedApiKey
{
    private string $value;

    public function __construct(
        string $apiKey,
        bool $isHashed = false
    ) {
        if (trim($apiKey) === '') {
            throw new EmptyApiKeyException;
        }

        $this->value = $isHashed ? $apiKey : Crypt::encryptString($apiKey);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function decrypt(): string
    {
        return Crypt::decryptString($this->value);
    }
}
