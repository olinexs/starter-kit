<?php

return [
    'default' => env('LDAP_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'hosts'            => [env('LDAP_HOST', '127.0.0.1')],
            'username'         => env('LDAP_USERNAME', 'cn=user,dc=local,dc=com'),
            'password'         => env('LDAP_PASSWORD', ''),
            'port'             => env('LDAP_PORT', 389),
            'base_dn'          => env('LDAP_BASE_DN', 'dc=local,dc=com'),
            'timeout'          => 5,
            'use_ssl'          => env('LDAP_SSL', false),
            'use_tls'          => env('LDAP_TLS', false),
            'use_sasl'         => env('LDAP_SASL', false),
        ],
    ],

    'cache' => [
        'enabled'  => env('LDAP_CACHE', false),
        'driver'   => 'file',
    ],
];
