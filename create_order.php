<?php
session_start();
require_once "config/razorpay.php";

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['amount'])) {
    echo json_encode(['error' => 'Invalid request data']);
    exit;
}

$amount = $data['amount']; // Amount in paise
$currency = $data['currency'] ?? 'INR';

try {
    // Create Razorpay order
    $orderData = [
        'amount' => $amount,
        'currency' => $currency,
        'receipt' => 'order_' . $_SESSION['user_id'] . '_' . time(),
        'notes' => [
            'user_id' => $_SESSION['user_id'],
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $order = json_decode($response, true);
        echo json_encode([
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency']
        ]);
    } else {
        echo json_encode(['error' => 'Failed to create order: ' . $response]);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
