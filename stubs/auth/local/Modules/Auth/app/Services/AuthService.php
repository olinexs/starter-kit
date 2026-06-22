<?php

namespace Modules\Auth\app\Services;

use Illuminate\Support\Facades\Hash;
use Modules\Auth\app\Repositories\AuthRepositoryInterface;

class AuthService
{
    public function __construct(private AuthRepositoryInterface $repo) {}

    public function login(array $credentials): ?array
    {
        $user = $this->repo->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
