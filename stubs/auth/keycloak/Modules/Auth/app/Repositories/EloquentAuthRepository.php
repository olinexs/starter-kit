<?php

namespace Modules\Auth\app\Repositories;

use App\Models\User;

class EloquentAuthRepository implements AuthRepositoryInterface
{
    public function findOrCreateFromKeycloak(array $userInfo): mixed
    {
        return User::firstOrCreate(
            ['keycloak_sub' => $userInfo['sub']],
            [
                'name'     => $userInfo['name'] ?? $userInfo['preferred_username'],
                'email'    => $userInfo['email'] ?? '',
                'password' => '',
            ]
        );
    }
}
