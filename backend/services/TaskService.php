<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/TaskDAO.php';

class TaskService extends BaseService {
    public function __construct() {
        parent::__construct(new TaskDAO());
    }

    // Get tasks by user ID
    public function getTasksByUserId($userId) {
        return $this->dao->getByUserId($userId);
    }

    // Create a task
    public function createTask($taskData) {
        if ($this->validateTaskData($taskData)) {
            return $this->create($taskData); // Reuses BaseService's create method
        }
        throw new Exception("Invalid task data.");
    }

    // Update a task
    public function updateTask($taskId, $taskData) {
        if ($this->validateTaskData($taskData)) {
            return $this->update($taskId, $taskData); // Reuses BaseService's update method
        }
        throw new Exception("Invalid task data.");
    }

    // Validate task data
    private function validateTaskData($data) {
        if (empty($data['title']) || empty($data['description'])) {
            throw new Exception("Task title and description are required.");
        }
        return true;
    }
}
?>
