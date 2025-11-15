<?php
// Security configuration

return [
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'token_name' => 'csrf_token',
        'token_length' => 32,
        'regenerate' => true,
    ],

    // Password settings
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
        'hash_algorithm' => PASSWORD_DEFAULT,
        'cost' => 12,
    ],

    // Rate limiting
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 5,
        'decay_minutes' => 1,
        'lockout_minutes' => 15,
    ],

    // Input validation
    'validation' => [
        'sanitize_input' => true,
        'allow_html' => false,
        'max_input_length' => 10000,
    ],

    // XSS Protection
    'xss' => [
        'enabled' => true,
        'allowed_tags' => ['<p>', '<br>', '<strong>', '<em>', '<u>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<ul>', '<ol>', '<li>', '<a>'],
    ],

    // Encryption
    'encryption' => [
        'key' => 'your-encryption-key-here', // Change this in production
        'cipher' => 'AES-256-CBC',
    ],

    // Headers
    'headers' => [
        'hsts' => [
            'enabled' => false,
            'max_age' => 31536000,
            'include_subdomains' => false,
        ],
        'csp' => [
            'enabled' => false,
            'policy' => "default-src 'self'",
        ],
    ],
];