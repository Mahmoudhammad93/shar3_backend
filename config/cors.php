<?php

$frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
$extraOrigins = array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', ''))));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_filter([
        $frontendUrl,
        'http://localhost:3000',
        'http://localhost:3001',
        'https://share3a.chiefcoder.net',
        'https://shar3.chiefcoder.net',
        ...$extraOrigins,
    ]))),
    'allowed_origins_patterns' => [
        '#^https://[\w-]+\.chiefcoder\.net$#',
        '#^http://localhost:\d+$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
