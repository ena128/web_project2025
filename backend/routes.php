<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/TaskService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/PriorityService.php';
require_once __DIR__ . '/services/ActivityLogsService.php';

Flight::route('GET /users', function() {
    $service = new UserService();
    Flight::json($service->getAll());
});

Flight::route('GET /users/@id', function($id) {
    $service = new UserService();
    Flight::json($service->getById($id));
});

Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    $service = new UserService();
    Flight::json($service->create($data));
});

Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    $service = new UserService();
    Flight::json($service->update($id, $data));
});

Flight::route('DELETE /users/@id', function($id) {
    $service = new UserService();
    Flight::json($service->delete($id));
});

// Repeat for Tasks, Categories, Priorities, and ActivityLogs
Flight::route('GET /tasks', function() {
    $service = new TaskService();
    Flight::json($service->getAll());
});

Flight::route('POST /tasks', function() {
    $data = Flight::request()->data->getData();
    $service = new TaskService();
    Flight::json($service->create($data));
});

// Add routes for Categories
Flight::route('GET /categories', function() {
    $service = new CategoryService();
    Flight::json($service->getAll());
});

// Add routes for Priorities
Flight::route('GET /priorities', function() {
    $service = new PriorityService();
    Flight::json($service->getAll());
});

// Add routes for ActivityLogs
Flight::route('GET /logs', function() {
    $service = new ActivityLogsService();
    Flight::json($service->getAll());
});
Flight::route('/', function() {
    echo 'API is working!';
});
Flight::route('GET /docs/swagger.json', function () {
    header('Content-Type: application/json');
    echo file_get_contents(__DIR__ . '/docs/swagger.json');
});


// Start the FlightPHP framework
Flight::start();
?>
