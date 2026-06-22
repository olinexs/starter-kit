<?php

namespace Modules\Auth\app\Repositories;

interface AuthRepositoryInterface
{
    public function findOrCreateFromLdap(mixed $ldapUser): mixed;
}
