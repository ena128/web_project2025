<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/PriorityDAO.php';

class PriorityService extends BaseService {
    public function __construct() {
        parent::__construct(new PriorityDAO());
    }

    // Create a new priority
    public function createPriority($priorityData) {
        if ($this->validatePriorityData($priorityData)) {
            return $this->create($priorityData); // Reuses BaseService's create method
        }
        throw new Exception("Invalid priority data.");
    }

    // Get all priorities
    public function getPriorities() {
        return $this->dao->getAll();
    }

    // Validate priority data
    private function validatePriorityData($data) {
        if (empty($data['name'])) {
            throw new Exception("Priority name is required.");
        }
        return true;
    }
}
?>
