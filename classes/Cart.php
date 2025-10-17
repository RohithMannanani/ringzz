<?php
// classes/Cart.php
require_once __DIR__.'/Product.php';
class Cart {
    private $productModel;
    public function __construct(){
        $this->productModel = new Product();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    }
    public function add($productId, $qty=1){
        $cart = &$_SESSION['cart'];
        if (isset($cart[$productId])) $cart[$productId] += $qty;
        else $cart[$productId] = $qty;
        return true;
    }
    public function remove($productId){
        $cart = &$_SESSION['cart'];
        if (isset($cart[$productId])) unset($cart[$productId]);
    }

    public function removeOne($productId){
        $cart = &$_SESSION['cart'];
        if (isset($cart[$productId])) {
            $cart[$productId]--;
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
        }
    }
    public function getItems(){
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        foreach($cart as $pid => $qty){
            $p = $this->productModel->getById($pid);
            if ($p) {
                $p['qty'] = $qty;
                $p['subtotal'] = $qty * floatval($p['price']);
                $items[] = $p;
            }
        }
        return $items;
    }
    public function getTotal(){
        $items = $this->getItems();
        $sum = 0;
        foreach($items as $it) $sum += $it['subtotal'];
        return $sum;
    }
    public function clear(){
        $_SESSION['cart'] = [];
    }
}

