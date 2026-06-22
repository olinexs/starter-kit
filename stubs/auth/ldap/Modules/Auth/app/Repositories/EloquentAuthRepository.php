<?php

namespace Modules\Auth\app\Repositories;

use App\Models\User;

class EloquentAuthRepository implements AuthRepositoryInterface
{
    public function findOrCreateFromLdap(mixed $ldapUser): mixed
    {
        return User::firstOrCreate(
            ['username' => $ldapUser->getFirstAttribute('samaccountname')],
            [
                'name'     => $ldapUser->getFirstAttribute('cn'),
                'email'    => $ldapUser->getFirstAttribute('mail'),
                'password' => '',
            ]
        );
    }
}
