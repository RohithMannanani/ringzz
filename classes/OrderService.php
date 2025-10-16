<?php
// classes/OrderService.php
require_once __DIR__.'/DB.php';
class OrderService {
    private $conn;
    public function __construct(){
        $this->conn = DB::getConnection();
    }

    // amount must be in paise (integer)
    public function createRazorpayOrder($amount_paise, $currency='INR') {
        $url = "https://api.razorpay.com/v1/orders";
        $data = json_encode([
            "amount" => intval($amount_paise),
            "currency" => $currency,
            "payment_capture" => 1
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) return ['error' => $err];

        $resp = json_decode($response, true);
        return $resp; // contains id (order_id), amount, currency, etc.
    }

    public function saveOrderLocal($razorpay_order_id, $amount){
        $stmt = $this->conn->prepare("INSERT INTO orders (razorpay_order_id, amount, status) VALUES (?,?, 'created')");
        $stmt->bind_param("sd", $razorpay_order_id, $amount);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function markPayment($razorpay_order_id, $payment_id, $signature, $status='paid'){
        $stmt = $this->conn->prepare("UPDATE orders SET razorpay_payment_id=?, razorpay_signature=?, status=? WHERE razorpay_order_id=?");
        $stmt->bind_param("ssss", $payment_id, $signature, $status, $razorpay_order_id);
        $stmt->execute();
    }

    // verify signature (recommended)
    public function verifySignature($attributes) {
        // attributes: [ 'razorpay_order_id','razorpay_payment_id','razorpay_signature' ]
        $order_id = $attributes['razorpay_order_id'];
        $payment_id = $attributes['razorpay_payment_id'];
        $signature = $attributes['razorpay_signature'];

        $payload = $order_id . '|' . $payment_id;
        $expected_signature = hash_hmac('sha256', $payload, RAZORPAY_KEY_SECRET);
        return hash_equals($expected_signature, $signature);
    }
}
