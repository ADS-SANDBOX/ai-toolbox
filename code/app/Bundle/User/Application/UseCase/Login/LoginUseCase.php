<?php

namespace App\Bundle\User\Application\UseCase\Login;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\InvalidCredentialsException;
use App\Bundle\User\Domain\Exception\InvalidEmailException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\Email;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final readonly class LoginUseCase
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * @throws InvalidEmailException
     * @throws InvalidCredentialsException
     */
    public function __invoke(LoginDTO $loginDTO): string
    {
        $email = new Email(email: $loginDTO->email());

        $user = $this->userRepository->findByEmail(email: $email);

        if (! $user instanceof User || ! $user->password()->verify(plainPassword: $loginDTO->password())) {
            throw new InvalidCredentialsException;
        }

        $token = JWTAuth::fromUser(
            $this->userRepository->getModelFromUser(user: $user)
        );

        $user->setToken(token: $token);
        $this->userRepository->update(user: $user);

        return $token;
    }
}
