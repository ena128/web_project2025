<?php
require_once __DIR__ . '/routes.php';

Flight::route('/', function() {
    echo 'Hello, world!';
});

Flight::start();
