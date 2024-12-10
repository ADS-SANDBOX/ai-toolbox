<?php

namespace App\Bundle\GitAssistant\Application\UseCase\GeneratePullRequest;

use App\Bundle\GitAssistant\Domain\Exception\EmptyGitDiffException;
use App\Bundle\GitAssistant\Domain\Service\PullRequestGeneratorService;
use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Domain\Exception\UserOpenaiApiKeyMissingException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;

final readonly class GeneratePullRequestUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private PullRequestGeneratorService $pullRequestGeneratorService
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws UserOpenaiApiKeyMissingException
     * @throws EmptyGitDiffException
     */
    public function __invoke(GeneratePullRequestDTO $generatePullRequestDTO): array
    {
        $user = $this->userRepository->findById(id: $generatePullRequestDTO->userId());

        if (! $user instanceof User) {
            throw new UserNotFoundException(id: $generatePullRequestDTO->userId());
        }

        if (! $user->openaiApiKey() instanceof HashedApiKey) {
            throw new UserOpenaiApiKeyMissingException(id: $generatePullRequestDTO->userId());
        }

        if ($generatePullRequestDTO->gitDiff()->isEmpty()) {
            throw new EmptyGitDiffException;
        }

        return $this->pullRequestGeneratorService->generateDescription(
            gitDiff: $generatePullRequestDTO->gitDiff()->value(),
            apiKey: $user->openaiApiKey()->decrypt()
        );
    }
}
