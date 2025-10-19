<?php
// Razorpay Configuration with your keys
define('RAZORPAY_KEY_ID', 'rzp_test_5fIpDiq0CC4SjF');
define('RAZORPAY_KEY_SECRET', 'yKuiw8ieBLCqqBhukMYBTIRH');

function verifyPaymentSignature($order_id, $payment_id, $signature) {
    $generated_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, RAZORPAY_KEY_SECRET);
    return hash_equals($generated_signature, $signature);
}
?>