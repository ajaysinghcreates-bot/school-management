<?php
// Application configuration

return [
    // Application settings
    'name' => 'School Management System',
    'version' => '1.0.0',
    'environment' => 'development', // development, staging, production

    // Debug settings
    'debug' => true,
    'log_level' => 'debug',

    // Timezone
    'timezone' => 'UTC',

    // Base URL (set dynamically or configure)
    'base_url' => 'http://localhost',

    // Session settings
    'session' => [
        'name' => 'sms_session',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
    ],

    // File upload settings
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'path' => 'uploads/',
    ],

    // Pagination
    'pagination' => [
        'per_page' => 10,
        'max_links' => 5,
    ],

    // Cache settings
    'cache' => [
        'enabled' => false,
        'path' => 'cache/',
        'ttl' => 3600, // 1 hour
    ],

    // API settings
    'api' => [
        'rate_limit' => 100, // requests per minute
        'token_lifetime' => 86400, // 24 hours
    ],
];