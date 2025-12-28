<?php

// db/Database.php
class Database {

    private static $instance = null;   // static means it belongs to the class itself, not to an object.
    protected $conn;

    // 1️⃣ Make constructor PRIVATE
    private function __construct() { //why private ? Because we want only one instance, so nobody can create multiple objects.
        $this->conn = new mysqli("localhost", "root", "", "db3");

        if ($this->conn->connect_error) {
            die("DB Connection Failed: " . $this->conn->connect_error);
        }
    }

    // 2️⃣ Public method to get the ONE instance
    public static function getInstance() { //It checks if an instance already exists:: If not, it creates one using the private constructor.If yes, it returns the existing one.
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
    private function __clone() {} //clone creates a copy of an object.

}
?>