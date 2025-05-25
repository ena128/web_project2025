<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../services/AuthService.php';
require __DIR__ . '/../middleware/AuthMiddleware.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Register database connection
Flight::register('db', 'PDO', [
    'mysql:host=localhost;dbname=todomasterdb;charset=utf8',
    'root', // DB username
    '',     // DB password
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
]);

// Register services
Flight::register('auth_service', 'AuthService');
Flight::register('auth_middleware', 'AuthMiddleware');

// Global middleware: apply JWT verification to all routes except login/register
Flight::route('/*', function () {
    $path = Flight::request()->url;
    if (
        strpos($path, '/auth/login') === 0 ||
        strpos($path, '/auth/register') === 0
    ) {
        return true;
    }

    try {
        $token = Flight::request()->getHeader("Authentication");
        if (!$token) {
            Flight::halt(401, "Missing authentication header");
        }

        // Use middleware to verify the token
        if (Flight::auth_middleware()->verifyToken($token)) {
            return true;
        }

        Flight::halt(401, "Invalid token");
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage());
    }
});

// Load route files
require_once __DIR__ . '/../routes/AuthRoutes.php';
require_once __DIR__ . '/../routes/categories.php';
require_once __DIR__ . '/../routes/users.php';
require_once __DIR__ . '/../routes/priorities.php';
require_once __DIR__ . '/../routes/tasks.php';
require_once __DIR__ . '/../routes/activitylogs.php';

// Start Flight
Flight::start();
