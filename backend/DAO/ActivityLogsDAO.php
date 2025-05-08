<?php
require_once 'BaseDAO.php';

class ActivityLogsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("activitylogs", "log_id"); 
    }

    // Method to check if a user exists
    private function userExists($userId) {
        $userDAO = new UserDAO(); // Assuming you have a UserDAO to handle user-related queries
        return $userDAO->getById($userId) !== null;
    }

    // Override the create method to check if the user exists before adding the log
    public function create($data) {
        if (empty($data['user_id']) || !$this->userExists($data['user_id'])) {
            throw new Exception("User with ID {$data['user_id']} does not exist.");
        }

        // If the user exists, create the activity log
        return parent::create($data);
    }
}

?>
