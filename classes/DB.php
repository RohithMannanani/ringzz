<?php
// classes/DB.php
class DB {
    private static $instance = null;
    private $conn;

    private function __construct(){
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("DB conn error: ".$this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }

    public static function getConnection(){
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance->conn;
    }
}
