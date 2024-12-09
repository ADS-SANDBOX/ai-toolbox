<?php

namespace Tests\Unit\Bundle\User\Application\UseCase\UpdateApiKey;

use App\Bundle\User\Application\UseCase\UpdateApiKey\UpdateApiKeyDTO;
use App\Bundle\User\Application\UseCase\UpdateApiKey\UpdateApiKeyUseCase;
use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\EmptyApiKeyException;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;
use App\Bundle\User\Domain\ValueObject\HashedPassword;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\CreatesApplication;

final class UpdateApiKeyUseCaseTest extends TestCase
{
    use CreatesApplication;

    private UserRepository $userRepository;

    private UpdateApiKeyUseCase $updateApiKeyUseCase;

    #[Test]
    public function it_should_update_user_api_key_successfully(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $apiKey = 'sk-valid-api-key';

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );

        // Configure mock expectations
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function (User $updatedUser) use ($user, $apiKey) {
                // Verify user data remains unchanged except for API key
                $this->assertEquals($user->id(), $updatedUser->id());
                $this->assertEquals($user->email()->value(), $updatedUser->email()->value());
                $this->assertEquals($user->name(), $updatedUser->name());

                // Verify API key was updated and encrypted
                $this->assertInstanceOf(HashedApiKey::class, $updatedUser->openaiApiKey());
                $this->assertNotEquals($apiKey, $updatedUser->openaiApiKey()->value());
                $this->assertEquals($apiKey, $updatedUser->openaiApiKey()->decrypt());

                return true;
            }));

        // Act
        $this->updateApiKeyUseCase->execute(
            new UpdateApiKeyDTO(
                userId: $userId,
                apiKey: $apiKey
            )
        );
    }

    #[Test]
    public function it_should_throw_exception_when_user_not_found(): void
    {
        // Arrange
        $userId = 'invalid-user-id';

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        // Assert
        $this->expectException(UserNotFoundException::class);

        // Act
        $this->updateApiKeyUseCase->execute(
            new UpdateApiKeyDTO(
                userId: $userId,
                apiKey: 'any-api-key'
            )
        );
    }

    #[Test]
    public function it_should_update_api_key_when_user_already_has_one(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $oldApiKey = 'sk-old-api-key';
        $newApiKey = 'sk-new-api-key';

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        $user->setOpenaiApiKey(new HashedApiKey($oldApiKey));

        // Configure mock expectations
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function (User $updatedUser) use ($newApiKey) {
                // Verify new API key was set and encrypted
                $this->assertInstanceOf(HashedApiKey::class, $updatedUser->openaiApiKey());
                $this->assertEquals($newApiKey, $updatedUser->openaiApiKey()->decrypt());

                return true;
            }));

        // Act
        $this->updateApiKeyUseCase->execute(
            new UpdateApiKeyDTO(
                userId: $userId,
                apiKey: $newApiKey
            )
        );
    }

    #[Test]
    public function it_should_throw_exception_when_empty_api_key(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $emptyApiKey = '';

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        // Assert
        $this->expectException(EmptyApiKeyException::class);
        $this->expectExceptionMessage('API key cannot be empty');

        // Act
        $this->updateApiKeyUseCase->execute(
            new UpdateApiKeyDTO(
                userId: $userId,
                apiKey: $emptyApiKey
            )
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->updateApiKeyUseCase = new UpdateApiKeyUseCase($this->userRepository);
    }
}
