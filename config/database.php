<?php
// Database configuration

return [
    'host' => 'localhost',
    'database' => 'school_management',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'driver' => 'mysql',
    'port' => 3306,

    // Connection options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],

    // Pool settings
    'pool' => [
        'min_connections' => 1,
        'max_connections' => 10,
        'idle_timeout' => 60,
    ],
];