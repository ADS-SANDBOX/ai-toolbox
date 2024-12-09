<?php

namespace Tests\Feature\Bundle\User\ApiKey;

use App\Bundle\User\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpdateApiKeyTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    #[Test]
    public function authenticated_user_can_update_api_key(): void
    {
        // Arrange
        $apiKeyData = [
            'api_key' => 'sk-test-api-key-123',
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/update-api-key', $apiKeyData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'OpenAI API Key updated successfully',
            ]);

        $user = UserModel::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user->openai_api_key);
        $this->assertNotEquals('sk-test-api-key-123', $user->openai_api_key);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->postJson('/api/update-api-key', [
            'api_key' => 'sk-test-api-key-123',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_validates_api_key_format(): void
    {
        // Test empty API key
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/update-api-key', [
            'api_key' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['api_key']);

        // Test with non-string API key
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/update-api-key', [
            'api_key' => 123,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['api_key']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Register a user and get token for all tests
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->token = $response->json('token');
    }
}
