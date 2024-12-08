<?php

namespace App\Bundle\User\Application\UseCase\RegisterUser;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\UserAlreadyExistsException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedPassword;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final readonly class RegisterUserUseCase
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function execute(RegisterUserDTO $registerUserDTO): string
    {
        $email = new Email(email: $registerUserDTO->email());

        if ($this->userRepository->findByEmail(email: $email) instanceof User) {
            throw new UserAlreadyExistsException(email: $email->value());
        }

        $user = new User(
            id: Str::uuid()->toString(),
            email: $email,
            hashedPassword: new HashedPassword(plainPassword: $registerUserDTO->password()),
            name: $registerUserDTO->name()
        );

        $this->userRepository->save(user: $user);

        $token = $this->generateToken(user: $user);
        $user->setToken(token: $token);

        $this->userRepository->update(user: $user);

        return $token;
    }

    private function generateToken(User $user): string
    {
        return JWTAuth::fromUser(
            $this->userRepository->getModelFromUser(user: $user)
        );
    }
}
