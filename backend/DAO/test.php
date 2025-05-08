<?php
require_once __DIR__ . '\BaseDAO.php';
require_once __DIR__ . '\UserDAO.php';
require_once __DIR__ . '\TaskDAO.php';
require_once __DIR__ . '\CategoryDAO.php';
require_once __DIR__ . '\PriorityDAO.php';
require_once __DIR__ . '\ActivityLogsDAO.php';

$userDAO = new UserDAO();
$taskDAO = new TaskDAO();
$categoryDAO = new CategoryDAO();
$priorityDAO = new PriorityDAO();
$activityLogsDAO = new ActivityLogsDAO();

echo "===== TESTING CRUD OPERATIONS =====\n";


echo "\n Creating test \n";

// Create a new user
$newUser = [
    'name' => 'Ena',
    'email' => 'zara13114567@example.com',
    'password' => password_hash('password123', PASSWORD_BCRYPT),
    'role' => 'user'
];
$userId = $userDAO->create($newUser);
echo "Created User ID: $userId\n";

// Create a new category
$newCategory = ['name' => 'Personal'];
$categoryId = $categoryDAO->create($newCategory);
echo "Created Category ID: $categoryId\n";

// Create a new priority
$newPriority = ['name' => 'Medium', 'color' => '#FFA500'];
$priorityId = $priorityDAO->create($newPriority);
echo "Created Priority ID: $priorityId\n";

// Create a new task
$newTask = [
    'user_id' => $userId,
    'title' => 'Buy Groceries',
    'due_date' => '2025-04-15 12:00:00',
    'status' => 'toDo',
    'priority_id' => $priorityId,
    'category_id' => $categoryId
];
$taskId = $taskDAO->create($newTask);
echo "Created Task ID: $taskId\n";

// **TEST READ**
echo "\n[READ TEST]\n";

// Get user by ID
$user = $userDAO->getById($userId);
print_r($user);

// Get task by ID
$task = $taskDAO->getById($taskId);
print_r($task);

// **TEST UPDATE**
echo "\n[UPDATE TEST]\n";

// Update user name
$updateUser = ['name' => 'Updated Test User'];
$userDAO->update($userId, $updateUser);
$updatedUser = $userDAO->getById($userId);
print_r($updatedUser);

// Update task title
$updateTask = ['title' => 'Updated Task Title'];
$taskDAO->update($taskId, $updateTask);
$updatedTask = $taskDAO->getById($taskId);
print_r($updatedTask);

// **TEST DELETE**
echo "\n[DELETE TEST]\n";

// Delete task
$taskDAO->delete($taskId);
$deletedTask = $taskDAO->getById($taskId);
echo $deletedTask ? "Task deletion failed!\n" : "Task deleted successfully.\n";

/* Delete user
$userDAO->delete($userId);
$deletedUser = $userDAO->getById($userId);
echo $deletedUser ? "User deletion failed!\n" : "User deleted successfully.\n";*/

?>
