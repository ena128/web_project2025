<?php
require_once 'BaseDAO.php';

class CategoryDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("categories", "category_id");
    }

    // Get all categories
    public function getAllCategories() {
        return $this->getAll();  // Uses getAll from BaseDAO
    }

    // Get a category by its ID
    public function getCategoryById($category_id) {
        return $this->getById($category_id);
    }

    // Create a new category
    public function createCategory($name) {
        $queryData = ['name' => $name];
        return $this->create($queryData);
    }

    // Update a category
    public function updateCategory($category_id, $name) {
        $queryData = ['name' => $name];
        return $this->update($category_id, $queryData);
    }

    // Delete a category
    public function deleteCategory($category_id) {
        return $this->delete($category_id);
    }
}
?>
