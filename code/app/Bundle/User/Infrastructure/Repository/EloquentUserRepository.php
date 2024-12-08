<?php

namespace App\Bundle\User\Infrastructure\Repository;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\Exception\InvalidEmailException;
use App\Bundle\User\Domain\Exception\InvalidPasswordException;
use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Domain\ValueObject\HashedApiKey;
use App\Bundle\User\Domain\ValueObject\HashedPassword;
use App\Bundle\User\Infrastructure\Persistence\Eloquent\UserModel;

final class EloquentUserRepository implements UserRepository
{
    public function save(User $user): void
    {
        UserModel::create([
            'id' => $user->id(),
            'name' => $user->name(),
            'email' => $user->email()->value(),
            'password' => $user->password()->value(),
            'token' => $user->token(),
        ]);
    }

    public function update(User $user): void
    {
        UserModel::where('id', $user->id())
            ->update([
                'name' => $user->name(),
                'email' => $user->email()->value(),
                'password' => $user->password()->value(),
                'token' => $user->token(),
                'openai_api_key' => $user->openaiApiKey()?->value(),
            ]);
    }

    public function findByEmail(Email $email): ?User
    {
        $userModel = UserModel::where('email', $email->value())->first();

        if (! $userModel) {
            return null;
        }

        return $this->toEntity(userModel: $userModel);
    }

    /**
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     */
    private function toEntity(UserModel $userModel): User
    {
        $user = new User(
            id: $userModel->id,
            email: new Email(email: $userModel->email),
            hashedPassword: new HashedPassword(
                plainPassword: $userModel->password,
                isHashed: true
            ),
            name: $userModel->name
        );

        if ($userModel->token) {
            $user->setToken(token: $userModel->token);
        }

        if ($userModel->openai_api_key) {
            $user->setOpenaiApiKey(
                hashedApiKey: new HashedApiKey(
                    apiKey: $userModel->openai_api_key,
                    isHashed: true
                )
            );
        }

        return $user;
    }

    public function findById(string $id): ?User
    {
        $userModel = UserModel::find($id);

        if (! $userModel) {
            return null;
        }

        return $this->toEntity(userModel: $userModel);
    }

    public function getModelFromUser(User $user): UserModel
    {
        return UserModel::find($user->id());
    }
}
