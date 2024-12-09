<?php

namespace App\Bundle\GitAssistant\Infrastructure\Action;

use App\Bundle\GitAssistant\Application\UseCase\GenerateCommit\GenerateCommitDTO;
use App\Bundle\GitAssistant\Application\UseCase\GenerateCommit\GenerateCommitUseCase;
use App\Bundle\GitAssistant\Infrastructure\Http\Request\GenerateCommitRequest;
use App\Bundle\GitAssistant\Infrastructure\Http\Response\CommitGeneratedResponse;
use Exception;
use Illuminate\Http\JsonResponse;

final readonly class GenerateCommitAction
{
    public function __construct(
        private GenerateCommitUseCase $generateCommitUseCase
    ) {}

    public function __invoke(GenerateCommitRequest $generateCommitRequest): JsonResponse
    {
        try {
            $commitMessage = $this->generateCommitUseCase->execute(
                generateCommitDTO: new GenerateCommitDTO(
                    gitDiff: $generateCommitRequest->get(key: 'git_diff'),
                    userId: $generateCommitRequest->user()->id
                )
            );

            return (new CommitGeneratedResponse(commitMessage: $commitMessage))->toResponse();

        } catch (Exception $e) {
            return response()->json(
                data: ['error' => $e->getMessage()],
                status: JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
