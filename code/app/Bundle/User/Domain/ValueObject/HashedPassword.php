<?php

namespace App\Bundle\User\Domain\ValueObject;

use App\Bundle\User\Domain\Exception\InvalidPasswordException;
use Illuminate\Support\Facades\Hash;

final readonly class HashedPassword
{
    private string $value;

    /**
     * @throws InvalidPasswordException
     */
    public function __construct(
        string $plainPassword,
        bool $isHashed = false
    ) {
        if ($isHashed) {
            $this->value = $plainPassword;
        } else {
            $this->validate(password: $plainPassword);
            $this->value = Hash::make($plainPassword);
        }
    }

    /**
     * @throws InvalidPasswordException
     */
    private function validate(string $password): void
    {
        if (strlen($password) < 8) {
            throw new InvalidPasswordException(message: 'Password must be at least 8 characters long');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function verify(string $plainPassword): bool
    {
        return Hash::check($plainPassword, $this->value);
    }
}
