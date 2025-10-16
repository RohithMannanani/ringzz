<?php
// classes/Product.php
require_once __DIR__.'/DB.php';
class Product {
    private $conn;
    public function __construct(){
        $this->conn = DB::getConnection();
    }
    public function getAll(){
        $res = $this->conn->query("SELECT * FROM products");
        $rows = [];
        while($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }
    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res;
    }
    public function add($name, $description, $price, $stock, $imagePath){
    $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $stock, $imagePath);
    return $stmt->execute();
}

}
