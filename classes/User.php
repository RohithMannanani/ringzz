<?php
class User {
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }

    public function register($phone, $username, $password, $confirm){
        if($password !== $confirm){
            return ['success'=>false, 'message'=>'Passwords do not match'];
        }
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            return ['success'=>false, 'message'=>'Phone already registered'];
        }
        $stmt->close();

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users(phone, username, password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $phone, $username, $hashed);
        $stmt->execute();
        $stmt->close();
        return ['success'=>true, 'message'=>'Registration successful'];
    }

    public function login($phone, $password){
        $stmt = $this->conn->prepare("SELECT id, username, password FROM users WHERE phone=?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()){
            if(password_verify($password, $row['password'])){
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                return ['success' => true, 'username' => $row['username']];
            } else {
                return ['success'=>false, 'message'=>'Invalid password'];
            }
        } else {
            return ['success'=>false, 'message'=>'User not found'];
        }
    }
}
?>
