<?php

namespace App\Bundle\GitAssistant\Infrastructure\Service;

use App\Bundle\GitAssistant\Domain\Exception\OpenAI\InvalidApiKeyException;
use App\Bundle\GitAssistant\Domain\Exception\OpenAI\ServiceUnavailableException;
use App\Bundle\GitAssistant\Domain\Service\PullRequestGeneratorService;
use Exception;
use OpenAI;

final readonly class OpenAIPullRequestGenerator implements PullRequestGeneratorService
{
    private const SYSTEM_PROMPT = <<<'EOF'
        You are a Pull Request description generator. Analyze the provided git diff and generate a concise and professional PR description following these rules:
        1. Must be in English.
        2. Use a structured format with the following sections:
           - **Summary**: Provide a brief overview of the purpose of the changes.
           - **Key Changes**: Use bullet points to outline the main modifications, focusing on updated functions, added/removed features, and affected files.
           - **Technical Details**: Include critical implementation details that developers or reviewers should be aware of, such as algorithms, patterns, or configurations introduced or altered.
           - **Impact**: Explain how these changes affect the system, highlighting potential risks, dependencies, or areas of the application that may require extra attention.
           - **Notes**: (Optional) Add any additional information, such as references to related tickets, future improvements, or migration steps.
        3. All sentences must end with a period.
        4. Focus on the most critical aspects, avoiding unnecessary details.
        5. Highlight any breaking changes prominently.
        6. Ensure the tone is professional and the language is clear and concise.
        EOF;

    public function __construct(
        private PullRequestResponseCache $pullRequestResponseCache
    ) {}

    /**
     * @throws InvalidApiKeyException
     * @throws ServiceUnavailableException
     */
    public function generateDescription(string $gitDiff, string $apiKey): array
    {
        // Check cache first
        $cached = $this->pullRequestResponseCache->get(gitDiff: $gitDiff);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $client = OpenAI::client($apiKey);

            $createResponse = $client->chat()->create(parameters: [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user', 'content' => $gitDiff],
                ],
                'temperature' => 0.4,
                'max_tokens' => 1000,
            ]);

            $description = trim($createResponse->choices[0]->message->content);

            // Cache the response
            return $this->pullRequestResponseCache->put(
                gitDiff: $gitDiff,
                description: $description
            );

        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Incorrect API key provided')) {
                throw new InvalidApiKeyException;
            }

            throw new ServiceUnavailableException;
        }
    }
}
