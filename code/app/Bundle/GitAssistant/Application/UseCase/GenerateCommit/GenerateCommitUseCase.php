<?php

namespace App\Bundle\GitAssistant\Application\UseCase\GenerateCommit;

use App\Bundle\GitAssistant\Domain\Exception\EmptyGitDiffException;
use App\Bundle\GitAssistant\Domain\Service\CommitGeneratorService;
use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Domain\Exception\UserOpenaiApiKeyMissingException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;

final readonly class GenerateCommitUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private CommitGeneratorService $commitGeneratorService
    ) {}

    /**
     * @throws EmptyGitDiffException|UserOpenaiApiKeyMissingException|UserNotFoundException
     */
    public function execute(GenerateCommitDTO $generateCommitDTO): array
    {
        $user = $this->userRepository->findById(id: $generateCommitDTO->userId());

        if (! $user instanceof User) {
            throw new UserNotFoundException(id: $generateCommitDTO->userId());
        }

        if (! $user->openaiApiKey() instanceof HashedApiKey) {
            throw new UserOpenaiApiKeyMissingException(id: $generateCommitDTO->userId());
        }

        if ($generateCommitDTO->gitDiff()->isEmpty()) {
            throw new EmptyGitDiffException;
        }

        return $this->commitGeneratorService->generateMessage(
            gitDiff: $generateCommitDTO->gitDiff()->value(),
            apiKey: $user->openaiApiKey()->decrypt()
        );
    }
}
