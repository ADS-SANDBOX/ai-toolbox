<?php

namespace Tests\Unit\Bundle\GitAssistant\Application\UseCase\GenerateCommit;

use App\Bundle\GitAssistant\Application\UseCase\GenerateCommit\GenerateCommitDTO;
use App\Bundle\GitAssistant\Application\UseCase\GenerateCommit\GenerateCommitUseCase;
use App\Bundle\GitAssistant\Domain\Exception\EmptyGitDiffException;
use App\Bundle\GitAssistant\Domain\Service\CommitGeneratorService;
use App\Bundle\GitAssistant\Domain\ValueObject\GitDiff;
use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Domain\Exception\UserOpenaiApiKeyMissingException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;
use App\Bundle\User\Domain\ValueObject\HashedPassword;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\CreatesApplication;

final class GenerateCommitUseCaseTest extends TestCase
{
    use CreatesApplication;

    private UserRepository $userRepository;

    private CommitGeneratorService $commitGeneratorService;

    private GenerateCommitUseCase $generateCommitUseCase;

    /**
     * @throws UserNotFoundException
     * @throws UserOpenaiApiKeyMissingException
     * @throws EmptyGitDiffException
     */
    #[Test]
    public function it_should_generate_commit_message_successfully(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $gitDiff = 'diff --git a/file.txt b/file.txt...';
        $apiKey = 'sk-test-api-key';

        $expectedResponse = [
            'message' => 'feat: Add new feature',
            'cached' => false,
            'expires_at' => '2024-12-10T00:00:00Z',
        ];

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        $user->setOpenaiApiKey(new HashedApiKey($apiKey));

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->commitGeneratorService
            ->expects($this->once())
            ->method('generateMessage')
            ->with($gitDiff, $apiKey)
            ->willReturn($expectedResponse);

        // Act
        $result = ($this->generateCommitUseCase)(
            new GenerateCommitDTO(
                gitDiff: new GitDiff($gitDiff),
                userId: $userId
            )
        );

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws UserNotFoundException
     * @throws UserOpenaiApiKeyMissingException
     */
    #[Test]
    public function it_should_throw_exception_when_git_diff_is_empty(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $emptyGitDiff = '';

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        $user->setOpenaiApiKey(new HashedApiKey('sk-test-api-key'));

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        // Assert
        $this->expectException(EmptyGitDiffException::class);

        // Act
        ($this->generateCommitUseCase)(
            new GenerateCommitDTO(
                gitDiff: new GitDiff($emptyGitDiff),
                userId: $userId
            )
        );
    }

    /**
     * @throws UserNotFoundException
     * @throws EmptyGitDiffException
     */
    #[Test]
    public function it_should_throw_exception_when_user_api_key_is_missing(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $gitDiff = 'diff --git a/file.txt b/file.txt...';

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        // Note: No API key set

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        // Assert
        $this->expectException(UserOpenaiApiKeyMissingException::class);

        // Act
        ($this->generateCommitUseCase)(
            new GenerateCommitDTO(
                gitDiff: new GitDiff($gitDiff),
                userId: $userId
            )
        );
    }

    /**
     * @throws UserNotFoundException
     * @throws UserOpenaiApiKeyMissingException
     * @throws EmptyGitDiffException
     */
    #[Test]
    public function it_should_handle_special_characters_in_git_diff(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $gitDiff = "diff --git a/file.txt b/file.txt\n+Special €hars: ñ, á, 漢字\n-Normal text";
        $apiKey = 'sk-test-api-key';

        $expectedResponse = [
            'message' => 'fix: Handle special characters',
            'cached' => false,
            'expires_at' => '2024-12-10T00:00:00Z',
        ];

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        $user->setOpenaiApiKey(new HashedApiKey($apiKey));

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->commitGeneratorService
            ->expects($this->once())
            ->method('generateMessage')
            ->with($gitDiff, $apiKey)
            ->willReturn($expectedResponse);

        // Act
        $result = ($this->generateCommitUseCase)(
            new GenerateCommitDTO(
                gitDiff: new GitDiff($gitDiff),
                userId: $userId
            )
        );

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws UserNotFoundException
     * @throws UserOpenaiApiKeyMissingException
     * @throws EmptyGitDiffException
     */
    #[Test]
    public function it_should_handle_large_git_diffs(): void
    {
        // Arrange
        $userId = 'valid-user-id';
        $largeDiff = str_repeat("diff --git a/file.txt b/file.txt\n+Some changes\n-Old content\n", 1000);
        $apiKey = 'sk-test-api-key';

        $expectedResponse = [
            'message' => 'refactor: Large scale changes',
            'cached' => false,
            'expires_at' => '2024-12-10T00:00:00Z',
        ];

        $user = new User(
            id: $userId,
            email: new Email('test@example.com'),
            hashedPassword: new HashedPassword('password123', true),
            name: 'Test User'
        );
        $user->setOpenaiApiKey(new HashedApiKey($apiKey));

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->commitGeneratorService
            ->expects($this->once())
            ->method('generateMessage')
            ->with($largeDiff, $apiKey)
            ->willReturn($expectedResponse);

        // Act
        $result = ($this->generateCommitUseCase)(
            new GenerateCommitDTO(
                gitDiff: new GitDiff($largeDiff),
                userId: $userId
            )
        );

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->commitGeneratorService = $this->createMock(CommitGeneratorService::class);
        $this->generateCommitUseCase = new GenerateCommitUseCase(
            $this->userRepository,
            $this->commitGeneratorService
        );
    }
}
