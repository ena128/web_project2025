<?php
// BaseService.php
abstract class BaseService {
    protected $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    // Generic method to create an entity
    public function create($data) {
        return $this->dao->create($data);
    }

    // Generic method to get an entity by ID
    public function getById($id) {
        return $this->dao->getById($id);
    }

    // Generic method to update an entity
    public function update($id, $data) {
        return $this->dao->update($id, $data);
    }

    // Generic method to delete an entity
    public function delete($id) {
        return $this->dao->delete($id);
    }
}
?>
