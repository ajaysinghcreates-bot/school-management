<?php
// Entry point for the school management system

// Load configuration
require_once 'config/app.php';
require_once 'config/database.php';

// Include core classes
require_once 'core/Database.php';
require_once 'core/Router.php';
require_once 'core/Security.php';
require_once 'core/Session.php';
require_once 'core/Validator.php';

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');

// If path is empty, default to home
if (empty($path)) {
    $path = 'home';
}

// Create router instance and dispatch
$router = new Router();
$router->dispatch($path);
?>