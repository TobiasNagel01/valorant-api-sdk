<?php

return [
    'base_url' => env('VALORANT_API_BASE_URL', 'https://api.henrikdev.xyz'),

    'key' => env('VALORANT_API_KEY', ''),

    /*
     * Where the API key is sent. Either "header" (Authorization) or "query" (api_key).
     */
    'auth_in' => env('VALORANT_API_AUTH_IN', 'header'),

    'timeout' => (int) env('VALORANT_API_TIMEOUT', 30),
];
