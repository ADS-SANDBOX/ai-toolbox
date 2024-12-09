<?php

namespace Tests\Feature\Bundle\User\Registration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully(): void
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePass123!',
        ];

        // Act
        $response = $this->postJson('/api/register', $userData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'name' => 'John Doe',
        ]);
    }

    #[Test]
    public function it_validates_registration_data(): void
    {
        $invalidData = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
        ];

        $response = $this->postJson('/api/register', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function it_prevents_duplicate_email_registration(): void
    {
        // Register first user
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePass123!',
        ];

        $this->postJson('/api/register', $userData);

        // Try to register another user with same email
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'User with email john.doe@example.com already exists',
            ]);
    }
}
