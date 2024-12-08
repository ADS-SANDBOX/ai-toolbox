<?php

namespace App\Bundle\User\Domain\Repository;

use App\Bundle\User\Domain\Entity\User;
use App\Bundle\User\Domain\ValueObject\Email;
use App\Bundle\User\Infrastructure\Persistence\Eloquent\UserModel;

interface UserRepository
{
    public function save(User $user): void;

    public function update(User $user): void;

    public function findByEmail(Email $email): ?User;

    public function findById(string $id): ?User;

    public function getModelFromUser(User $user): UserModel;
}
