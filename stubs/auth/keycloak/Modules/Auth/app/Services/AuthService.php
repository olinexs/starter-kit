<?php

namespace Modules\Auth\app\Services;

use Illuminate\Support\Facades\Http;
use Modules\Auth\app\Repositories\AuthRepositoryInterface;

class AuthService
{
    public function __construct(private AuthRepositoryInterface $repo) {}

    /**
     * Validate the Keycloak access token via the userinfo endpoint,
     * then find or create the local user and issue a Sanctum token.
     */
    public function validateToken(string $accessToken): ?array
    {
        $userInfo = $this->fetchKeycloakUserInfo($accessToken);

        if (! $userInfo || empty($userInfo['sub'])) {
            return null;
        }

        $user  = $this->repo->findOrCreateFromKeycloak($userInfo);
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

    private function fetchKeycloakUserInfo(string $accessToken): ?array
    {
        $baseUrl = rtrim(config('keycloak.base_url'), '/');
        $realm   = config('keycloak.realm');

        $response = Http::withToken($accessToken)
            ->get("{$baseUrl}/realms/{$realm}/protocol/openid-connect/userinfo");

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }
}
