<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/ActivityLogsDAO.php';

class ActivityLogsService extends BaseService {
    public function __construct() {
        parent::__construct(new ActivityLogsDAO());
    }

    // Get activity logs by user ID
    public function getActivityLogsByUserId($userId) {
        return $this->dao->getByUserId($userId);
    }

    // Create a new activity log
    public function createActivityLog($logData) {
        if ($this->validateLogData($logData)) {
            return $this->create($logData); // Reuses BaseService's create method
        }
        throw new Exception("Invalid activity log data.");
    }

    // Validate activity log data
    private function validateLogData($data) {
        if (empty($data['activity_type'])) {
            throw new Exception("Activity type is required.");
        }
        if (empty($data['description'])) {
            throw new Exception("Activity description is required.");
        }
        return true;
    }
}
?>
