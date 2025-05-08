<?php
require_once 'BaseDAO.php';

class TaskDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("tasks", "task_id");
    }

    // Optional custom method to get tasks by user ID
    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
