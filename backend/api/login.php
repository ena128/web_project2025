<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/UserService.php';

Flight::set('UserService', new UserService());

// Define route for POST /api/login
Flight::route('POST /api/login', function() {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password required']);
        return;
    }

    $email = $data['email'];
    $password = $data['password'];

    try {
        $userService = Flight::get('UserService');
        $user = $userService->getByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password']);
            return;
        }

        unset($user['password']);
        echo json_encode(['user' => $user]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
});

Flight::start();
