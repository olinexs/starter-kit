<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'ldap',
        ],
        'api' => [
            'driver'   => 'sanctum',
            'provider' => 'ldap',
        ],
    ],

    'providers' => [
        'ldap' => [
            'driver' => 'ldap',
            'model'  => LdapRecord\Models\ActiveDirectory\User::class,
            'rules'  => [],
            'scopes' => [],
            'database' => [
                'model'     => App\Models\User::class,
                'sync_passwords' => false,
                'password_column' => 'password',
            ],
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'ldap',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
