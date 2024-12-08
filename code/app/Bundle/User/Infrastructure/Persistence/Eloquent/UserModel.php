<?php

namespace App\Bundle\User\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

final class UserModel extends Authenticatable implements JWTSubject
{
    use HasUuids;

    protected $table = 'users';

    protected $fillable = ['id', 'name', 'email', 'password', 'token', 'openai_api_key'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
