<?php

namespace Modules\Auth\app\Repositories;

interface AuthRepositoryInterface
{
    public function findOrCreateFromKeycloak(array $userInfo): mixed;
}
