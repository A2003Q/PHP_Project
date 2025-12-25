<?php

// db/Database.php
class Database {

    private static $instance = null;   // Holds the ONE instance
    protected $conn;

    // 1️⃣ Make constructor PRIVATE
    private function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "db3");

        if ($this->conn->connect_error) {
            die("DB Connection Failed: " . $this->conn->connect_error);
        }
    }

    // 2️⃣ Public method to get the ONE instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // 3️⃣ Method to access the connection
    public function getConnection() {
        return $this->conn;
    }

    // 4️⃣ Prevent cloning
    private function __clone() {}

}




?>