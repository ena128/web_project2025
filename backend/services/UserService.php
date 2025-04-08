<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

class UserService extends BaseService {
    public function __construct() {
        parent::__construct(new UserDAO());
    }

    // Method to create a user
    public function createUser($userData) {
        // Validate user data before creating the user
        if ($this->validateUserData($userData)) {
            return $this->dao->create($userData);
        }
        throw new Exception("Invalid user data.");
    }

    // Method to get a user by their email
    public function getUserByEmail($email) {
        return $this->dao->getByEmail($email);
    }

    // Private method to validate user data
    private function validateUserData($data) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }

        if (strlen($data['password']) < 8) {
            throw new Exception("Password must be at least 8 characters.");
        }

        // Check if the email is already taken
        if ($this->dao->getByEmail($data['email'])) {
            throw new Exception("Email is already in use.");
        }

        return true;
    }

// Get user by ID
public function getUserById($userId) {
    return $this->dao->getById($userId);
}

// Update user data
public function updateUser($userId, $userData) {
    if ($this->validateUserData($userData)) {
        return $this->dao->update($userId, $userData);
    }
    throw new Exception("Invalid user data.");
}

// Delete user
public function deleteUser($userId) {
    return $this->dao->delete($userId);
}



// Other user management methods can be added as needed
}
?>
