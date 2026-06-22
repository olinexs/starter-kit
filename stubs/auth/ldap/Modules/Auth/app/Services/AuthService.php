<?php

namespace Modules\Auth\app\Services;

use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Modules\Auth\app\Repositories\AuthRepositoryInterface;

class AuthService
{
    public function __construct(private AuthRepositoryInterface $repo) {}

    public function login(array $credentials): ?array
    {
        try {
            $connection = Container::getDefaultConnection();
            $connection->auth()->attempt(
                $credentials['username'] . '@' . config('ldap.connections.default.base_dn'),
                $credentials['password'],
            );
        } catch (\Exception) {
            return null;
        }

        $ldapUser = LdapUser::where('samaccountname', $credentials['username'])->first();

        if (! $ldapUser) {
            return null;
        }

        $user  = $this->repo->findOrCreateFromLdap($ldapUser);
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
