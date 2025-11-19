<?php
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins_patterns' => [],
    'exposed_headers' => [],
    'max_age' => 0,
    'allowed_origins' => ['http://localhost:4200'],
    'allowed_headers' => ['*'],
    'allowed_methods' => ['*'],
    'supports_credentials' => false,

];

