<?php

namespace Modules\Auth\app\Repositories;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): mixed;
}
