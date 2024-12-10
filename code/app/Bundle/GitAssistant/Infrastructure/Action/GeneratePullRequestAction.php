<?php

namespace App\Bundle\GitAssistant\Infrastructure\Action;

use App\Bundle\GitAssistant\Application\UseCase\GeneratePullRequest\GeneratePullRequestDTO;
use App\Bundle\GitAssistant\Application\UseCase\GeneratePullRequest\GeneratePullRequestUseCase;
use App\Bundle\GitAssistant\Domain\ValueObject\GitDiff;
use App\Bundle\GitAssistant\Infrastructure\Http\Request\GeneratePullRequestRequest;
use App\Bundle\GitAssistant\Infrastructure\Http\Response\PullRequestGeneratedResponse;
use Exception;
use Illuminate\Http\JsonResponse;

final readonly class GeneratePullRequestAction
{
    public function __construct(
        private GeneratePullRequestUseCase $generatePullRequestUseCase
    ) {}

    public function __invoke(GeneratePullRequestRequest $generatePullRequestRequest): JsonResponse
    {
        try {
            $result = ($this->generatePullRequestUseCase)(
                generatePullRequestDTO: new GeneratePullRequestDTO(
                    gitDiff: new GitDiff(value: $generatePullRequestRequest->get(key: 'git_diff')),
                    userId: $generatePullRequestRequest->user()->id
                )
            );

            return (new PullRequestGeneratedResponse(
                description: $result['description'],
                cached: $result['cached'],
                expiresAt: $result['expires_at']
            ))->toResponse();

        } catch (Exception $e) {
            return response()->json(
                data: ['error' => $e->getMessage()],
                status: JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
