<?php

namespace App\Bundle\User\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

final class UserModel extends Model implements JWTSubject
{
    use HasUuids;

    protected $table = 'users';

    protected $fillable = ['id', 'name', 'email', 'password', 'token'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
