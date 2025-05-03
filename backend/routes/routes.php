<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/TaskService.php';
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../services/PriorityService.php';
require_once __DIR__ . '/../services/ActivityLogsService.php';

Flight::register('userService', 'UserService');
Flight::register('taskService', 'TaskService');
Flight::register('categoryService', 'CategoryService');
Flight::register('priorityService', 'PriorityService');
Flight::register('activityLogsService', 'ActivityLogsService');

require_once __DIR__ . '/users.php';
require_once __DIR__ . '/tasks.php';
require_once __DIR__ . '/categories.php';
require_once __DIR__ . '/priorities.php';
require_once __DIR__ . '/activitylogs.php';

Flight::start();
