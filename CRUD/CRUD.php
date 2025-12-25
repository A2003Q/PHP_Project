
<?php
require_once "../SQL/Database.php";

class CRUD extends Database {

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
