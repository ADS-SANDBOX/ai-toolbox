<?php

namespace App\Bundle\User\Application\UseCase\UpdateApiKey;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;

final readonly class UpdateApiKeyUseCase
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function execute(UpdateApiKeyDTO $updateApiKeyDTO): void
    {
        $user = $this->userRepository->findById(
            id: $updateApiKeyDTO->userId()
        );

        if (! $user instanceof User) {
            throw new UserNotFoundException(id: $updateApiKeyDTO->userId());
        }

        $user->setOpenaiApiKey(
            hashedApiKey: new HashedApiKey(apiKey: $updateApiKeyDTO->apiKey())
        );

        $this->userRepository->update(user: $user);
    }
}
