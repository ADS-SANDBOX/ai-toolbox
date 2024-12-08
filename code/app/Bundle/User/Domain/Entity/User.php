<?php

namespace App\Bundle\User\Domain\Entity;

use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;
use App\Bundle\User\Domain\ValueObject\HashedPassword;

final class User
{
    private ?string $token = null;

    private ?HashedApiKey $hashedApiKey = null;

    public function __construct(
        private readonly string $id,
        private readonly Email $email,
        private readonly HashedPassword $hashedPassword,
        private readonly string $name
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->hashedPassword;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function token(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function openaiApiKey(): ?HashedApiKey
    {
        return $this->hashedApiKey;
    }

    public function setOpenaiApiKey(?HashedApiKey $hashedApiKey): void
    {
        $this->hashedApiKey = $hashedApiKey;
    }
}
