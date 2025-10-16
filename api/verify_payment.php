<?php
require_once '../config.php';
require_once '../classes/OrderService.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

$razorpay_order_id = $_POST['razorpay_order_id'] ?? null;
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? null;
$razorpay_signature = $_POST['razorpay_signature'] ?? null;

if(!$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature){
    echo json_encode(['success'=>false, 'error'=>'Missing parameters']); exit;
}

$orderService = new OrderService();
$valid = $orderService->verifySignature([
    'razorpay_order_id' => $razorpay_order_id,
    'razorpay_payment_id' => $razorpay_payment_id,
    'razorpay_signature' => $razorpay_signature
]);

if($valid){
    $orderService->markPayment($razorpay_order_id, $razorpay_payment_id, $razorpay_signature, 'paid');
    // optionally save order_items from cart -> order_items table
    $cart = new Cart();
    $items = $cart->getItems();
    $conn = DB::getConnection();
    // get local order id
    $stmt = $conn->prepare("SELECT id FROM orders WHERE razorpay_order_id=? LIMIT 1");
    $stmt->bind_param("s", $razorpay_order_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $local_order_id = $res['id'] ?? 0;
    if($local_order_id){
        foreach($items as $it){
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
            $stmt2->bind_param("iiid", $local_order_id, $it['id'], $it['qty'], $it['price']);
            $stmt2->execute();
        }
    }
    // clear cart
    $cart->clear();
    echo json_encode(['success'=>true, 'local_order_id'=>$local_order_id]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Invalid signature']);
}
