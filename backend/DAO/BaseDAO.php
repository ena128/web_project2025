<?php
class BaseDAO {
    protected $connection;
    protected $tableName;
    protected $primaryKey;

    public function __construct($tableName, $primaryKey) {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
        
        try {
            $dsn = "mysql:host=localhost;dbname=todomasterdb;charset=utf8mb4";
            $username = "root";
            $password = "";
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Automatically detect primary key if not provided
            if ($primaryKey === null) {
                $this->primaryKey = $this->detectPrimaryKey();
            } else {
                $this->primaryKey = $primaryKey;
            }

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }


    private function detectPrimaryKey() {
        $stmt = $this->connection->prepare("SHOW KEYS FROM $this->tableName WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $primaryKeyData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $primaryKeyData ? $primaryKeyData['Column_name'] : 'id'; // Default to 'id' if no primary key found
    }


    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM $this->tableName");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM $this->tableName WHERE $this->primaryKey = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $stmt = $this->connection->prepare("INSERT INTO $this->tableName ($columns) VALUES ($values)");
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

  
    public function update($id, $data) {
        $fields = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $stmt = $this->connection->prepare("UPDATE $this->tableName SET $fields WHERE $this->primaryKey = :id");
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":id", $id);
        return $stmt->execute(); 
    }


    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM $this->tableName WHERE $this->primaryKey = :id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

   
    public function countAll() {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM $this->tableName");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
