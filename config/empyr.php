<?php

return [

    // CP Credentials
    'cp_client_id'             => env('EMPYR_CP_CLIENT_ID'),
    'cp_client_secret'         => env('EMPYR_CP_CLIENT_SECRET'),

    // Publisher Credentials
    'publisher_client_id'     => env('EMPYR_PUBLISHER_CLIENT_ID'),
    'publisher_client_secret' => env('EMPYR_PUBLISHER_CLIENT_SECRET'),

    // API URL's
    'api_base_url'  => env('EMPYR_API_BASE_URL', 'https://www.mogl.com/api/v2'),
    'api_token_url' => env('EMPYR_TOKEN_URL', 'https://www.mogl.com'),

    'debug' => true,
];
