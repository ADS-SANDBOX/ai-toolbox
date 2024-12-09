<?php

namespace App\Bundle\GitAssistant\Infrastructure\Service;

use Illuminate\Support\Facades\Redis;
use JsonException;

final readonly class PullRequestResponseCache
{
    private const TTL = 86400; // 24 hours in seconds

    private const PREFIX = 'pull_request_generator:';

    /**
     * Get cached response for a git diff
     */
    public function get(string $gitDiff): ?array
    {
        try {
            $key = $this->generateKey(gitDiff: $gitDiff);
            $cached = Redis::get($key);

            if (! $cached) {
                return null;
            }

            $data = json_decode($cached, true, 512, JSON_THROW_ON_ERROR);

            return [
                'description' => $data['description'],
                'cached' => true,
                'expires_at' => $data['expires_at'],
            ];

        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Generate a unique key for the git diff
     */
    private function generateKey(string $gitDiff): string
    {
        return self::PREFIX.md5($gitDiff);
    }

    /**
     * Cache a new response
     *
     * @throws JsonException
     */
    public function put(string $gitDiff, string $description): array
    {
        $expiresAt = now()->addSeconds(self::TTL)->toIso8601String();

        $data = [
            'description' => $description,
            'expires_at' => $expiresAt,
        ];

        Redis::setex(
            $this->generateKey(gitDiff: $gitDiff),
            self::TTL,
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        return [
            'description' => $description,
            'cached' => false,
            'expires_at' => $expiresAt,
        ];
    }
}
