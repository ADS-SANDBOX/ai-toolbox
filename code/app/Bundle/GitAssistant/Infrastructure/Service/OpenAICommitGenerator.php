<?php

namespace App\Bundle\GitAssistant\Infrastructure\Service;

use App\Bundle\GitAssistant\Domain\Exception\OpenAI\InvalidApiKeyException;
use App\Bundle\GitAssistant\Domain\Exception\OpenAI\ServiceUnavailableException;
use App\Bundle\GitAssistant\Domain\Service\CommitGeneratorService;
use Exception;
use OpenAI;

final readonly class OpenAICommitGenerator implements CommitGeneratorService
{
    private const SYSTEM_PROMPT = <<<'EOF'
        You are a commit message generator. Analyze the git diff and create a meaningful commit message following these rules:
        1. Must be in English.
        2. All sentences must end with a period.
        3. Must start with one of these prefixes according to the changes:
           - feature: for new features or enhancements
           - fix: for bug fixes
           - refactor: for code improvements without changing functionality
           - chore: for maintenance tasks, dependencies, etc
           - docs: for documentation changes
           - test: for test additions or modifications
        4. Keep it concise but descriptive.
        5. Focus on the "what" and "why", not the "how".
        6. Format the message with this structure:
           [prefix]: Short description ending with period.
           - Main change or feature.
           - Second important change.
           - Additional details if necessary.
           - Impact or motivation if relevant.
        EOF;

    /**
     * @throws InvalidApiKeyException
     * @throws ServiceUnavailableException
     */
    public function generateMessage(string $gitDiff, string $apiKey): string
    {
        try {

            $client = OpenAI::client($apiKey);

            $createResponse = $client->chat()->create(parameters: [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user', 'content' => $gitDiff],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            return trim($createResponse->choices[0]->message->content);
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Incorrect API key provided')) {
                throw new InvalidApiKeyException;
            }

            throw new ServiceUnavailableException;
        }
    }
}
