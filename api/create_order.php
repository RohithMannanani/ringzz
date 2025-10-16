<?php
require_once '../config.php';
require_once '../classes/Cart.php';
require_once '../classes/OrderService.php';

header('Content-Type: application/json');

$cart = new Cart();
$items = $cart->getItems();
$total_rupees = $cart->getTotal();

if($total_rupees <= 0){
    echo json_encode(['error'=>'Cart empty']); exit;
}

$amount_paise = intval(round($total_rupees * 100)); // paise
$orderService = new OrderService();
$razorResp = $orderService->createRazorpayOrder($amount_paise);

if(isset($razorResp['error'])){
    echo json_encode(['error'=>$razorResp['error']]); exit;
}
if(!isset($razorResp['id'])){
    echo json_encode(['error'=>'Invalid razorpay response']); exit;
}

// persist to local DB
$localOrderId = $orderService->saveOrderLocal($razorResp['id'], $total_rupees);

// return required info to client
echo json_encode([
    'id' => $razorResp['id'],
    'amount' => $razorResp['amount'],
    'currency' => $razorResp['currency'],
    'key_id' => RAZORPAY_KEY_ID
]);
