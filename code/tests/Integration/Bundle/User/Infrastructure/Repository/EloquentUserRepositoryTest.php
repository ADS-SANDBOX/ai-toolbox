<?php

namespace Tests\Integration\Bundle\User\Infrastructure\Repository;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;
use App\Bundle\User\Domain\ValueObject\HashedPassword;
use App\Bundle\User\Infrastructure\Repository\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $userRepository;

    #[Test]
    public function it_should_create_user_and_update_api_key(): void
    {
        // Arrange - Create User Entity
        $userId = Str::uuid()->toString();
        $user = new User(
            id: $userId,
            email: new Email('john.doe@example.com'),
            hashedPassword: new HashedPassword('SecurePass123!'),
            name: 'John Doe'
        );

        // Act - Save User
        $this->userRepository->save(user: $user);

        // Assert - Check User Was Created
        $foundUser = $this->userRepository->findById(id: $userId);

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id(), $foundUser->id());
        $this->assertEquals($user->email()->value(), $foundUser->email()->value());
        $this->assertEquals($user->name(), $foundUser->name());
        $this->assertNull($foundUser->openaiApiKey());

        // Arrange - Prepare API Key Update
        $apiKey = 'sk-'.Str::random(48);
        $foundUser->setOpenaiApiKey(new HashedApiKey($apiKey));

        // Act - Update User with API Key
        $this->userRepository->update(user: $foundUser);

        // Assert - Verify API Key Update
        $updatedUser = $this->userRepository->findById(id: $userId);

        $this->assertNotNull($updatedUser->openaiApiKey());
        $this->assertNotEquals($apiKey, $updatedUser->openaiApiKey()->value(), 'API key should be encrypted');
        $this->assertEquals($apiKey, $updatedUser->openaiApiKey()->decrypt(), 'Decrypted API key should match original');
    }

    #[Test]
    public function it_should_find_user_by_email(): void
    {
        // Arrange
        $email = 'jane.doe@example.com';
        $user = new User(
            id: Str::uuid()->toString(),
            email: new Email($email),
            hashedPassword: new HashedPassword('SecurePass123!'),
            name: 'Jane Doe'
        );

        $this->userRepository->save(user: $user);

        // Act
        $foundUser = $this->userRepository->findByEmail(
            email: new Email($email)
        );

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($email, $foundUser->email()->value());
    }

    #[Test]
    public function it_should_update_multiple_user_fields(): void
    {
        // Arrange
        $userId = Str::uuid()->toString();
        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('InitialPass123!'),
            name: 'Test User'
        );

        $this->userRepository->save(user: $user);

        // Act - Update multiple fields
        $foundUser = $this->userRepository->findById(id: $userId);
        $foundUser->setOpenaiApiKey(new HashedApiKey('sk-testkey123'));
        $foundUser->setToken('jwt-token-123');

        $this->userRepository->update(user: $foundUser);

        // Assert
        $updatedUser = $this->userRepository->findById(id: $userId);

        $this->assertEquals('jwt-token-123', $updatedUser->token());
        $this->assertNotNull($updatedUser->openaiApiKey());
        $this->assertEquals('sk-testkey123', $updatedUser->openaiApiKey()->decrypt());
    }

    #[Test]
    public function it_should_handle_concurrent_user_updates(): void
    {
        // Arrange
        $userId = Str::uuid()->toString();
        $user = new User(
            id: $userId,
            email: new Email('concurrent@example.com'),
            hashedPassword: new HashedPassword('SecurePass123!'),
            name: 'Concurrent User'
        );

        $this->userRepository->save(user: $user);

        // Act - Simulate concurrent updates
        $user1 = $this->userRepository->findById(id: $userId);
        $user2 = $this->userRepository->findById(id: $userId);

        $user1->setOpenaiApiKey(new HashedApiKey('sk-key1'));
        $user2->setOpenaiApiKey(new HashedApiKey('sk-key2'));

        $this->userRepository->update(user: $user1);
        $this->userRepository->update(user: $user2);

        // Assert
        $finalUser = $this->userRepository->findById(id: $userId);

        $this->assertEquals('sk-key2', $finalUser->openaiApiKey()->decrypt(),
            'Last update should take precedence');
    }

    #[Test]
    public function it_should_maintain_data_integrity_on_updates(): void
    {
        // Arrange
        $userId = Str::uuid()->toString();
        $originalApiKey = 'sk-original-key';

        $user = new User(
            id: $userId,
            email: new Email('integrity@example.com'),
            hashedPassword: new HashedPassword('SecurePass123!'),
            name: 'Integrity Test'
        );

        // Save initial user
        $this->userRepository->save(user: $user);

        // Act - Retrieve saved user and update fields
        $foundUser = $this->userRepository->findById(id: $userId);

        $foundUser->setOpenaiApiKey(new HashedApiKey($originalApiKey));
        $foundUser->setToken('new-token');

        $this->userRepository->update(user: $foundUser);

        // Assert - Check all fields maintained integrity
        $updatedUser = $this->userRepository->findById(id: $userId);

        $this->assertEquals('integrity@example.com', $updatedUser->email()->value());
        $this->assertEquals('Integrity Test', $updatedUser->name());
        $this->assertEquals('new-token', $updatedUser->token());
        $this->assertEquals($originalApiKey, $updatedUser->openaiApiKey()->decrypt());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = app(EloquentUserRepository::class);
    }

    protected function tearDown(): void
    {
        // Clean up any remaining test data
        parent::tearDown();
    }
}
