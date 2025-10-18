<?php
// Razorpay Configuration
class RazorpayConfig {
    public static $key_id = "rzp_test_YOUR_KEY_ID";
    public static $key_secret = "YOUR_KEY_SECRET";
    
    public static function verifyPaymentSignature($order_id, $payment_id, $signature) {
        $generated_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, self::$key_secret);
        return hash_equals($generated_signature, $signature);
    }
}
?>