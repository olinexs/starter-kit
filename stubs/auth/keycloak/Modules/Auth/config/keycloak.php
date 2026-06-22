<?php

return [
    'base_url'      => env('KEYCLOAK_BASE_URL', 'http://localhost:8080'),
    'realm'         => env('KEYCLOAK_REALM', 'master'),
    'client_id'     => env('KEYCLOAK_CLIENT_ID'),
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
];
