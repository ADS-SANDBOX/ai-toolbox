<?php

namespace App\Bundle\User\Domain\ValueObject;

use App\Bundle\User\Domain\Exception\InvalidEmailException;

final readonly class Email
{
    private string $value;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(string $email)
    {
        $this->validate(email: $email);
        $this->value = $email;
    }

    /**
     * @throws InvalidEmailException
     */
    private function validate(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException(email: $email);
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
