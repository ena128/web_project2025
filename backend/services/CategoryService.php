<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../DAO/CategoryDAO.php';

class CategoryService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoryDAO());
    }

    // Create a new category
    public function createCategory($categoryData) {
        if ($this->validateCategoryData($categoryData)) {
            return $this->create($categoryData); // Reuses BaseService's create method
        }
        throw new Exception("Invalid category data.");
    }

    // Get all categories
    public function getCategories() {
        return $this->dao->getAll();
    }

    // Validate category data
    private function validateCategoryData($data) {
        if (empty($data['name'])) {
            throw new Exception("Category name is required.");
        }
        return true;
    }
}
?>
