<?php

namespace Modules\Auth\app\Repositories;

use App\Models\User;

class EloquentAuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): mixed
    {
        return User::where('email', $email)->first();
    }
}
