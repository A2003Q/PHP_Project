
<?php
require_once "../SQL/Database.php";

class CRUD  {
    protected $conn;


 public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    protected function create($sql) {
        return $this->conn->query($sql);
    }

    protected function read($sql) {
        return $this->conn->query($sql);
    }

    protected function update($sql) {
        return $this->conn->query($sql);
    }

    protected function delete($sql) {
        return $this->conn->query($sql);
    }
}

?>
