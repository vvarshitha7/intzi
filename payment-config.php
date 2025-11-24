<?php
require_once 'config.php';

// Demo Payment Gateway Configuration
define('PAYMENT_GATEWAY_NAME', 'Intzi Pay');
define('PAYMENT_CURRENCY', 'INR');
define('PAYMENT_SERVICE_FEE', 50);

// Simulate payment processing (for demo purposes)
function processDemoPayment($booking_id, $amount, $payment_method, $card_number = null) {
    global $conn;
    
    // Simulate processing delay
    sleep(1);
    
    // Generate demo transaction ID
    $transaction_id = 'TXN' . time() . rand(1000, 9999);
    
    // 95% success rate for demo (to show both success and failure scenarios)
    $is_success = (rand(1, 100) <= 95);
    
    if($is_success) {
        // Update booking as successful
        $sql = "UPDATE bookings SET 
                payment_status = 'completed',
                payment_method = '$payment_method',
                payment_date = NOW(),
                booking_status = 'confirmed',
                razorpay_payment_id = '$transaction_id'
                WHERE booking_id = $booking_id";
        
        $conn->query($sql);
        
        return [
            'success' => true,
            'transaction_id' => $transaction_id,
            'message' => 'Payment successful'
        ];
    } else {
        // Payment failed
        return [
            'success' => false,
            'transaction_id' => $transaction_id,
            'message' => 'Payment declined by bank'
        ];
    }
}

// Validate card number (basic validation for demo)
function validateCardNumber($card_number) {
    $card_number = preg_replace('/\s+/', '', $card_number);
    return (strlen($card_number) >= 13 && strlen($card_number) <= 19 && ctype_digit($card_number));
}

// Get card type from number
function getCardType($card_number) {
    $card_number = preg_replace('/\s+/', '', $card_number);
    
    if (preg_match('/^4/', $card_number)) {
        return 'Visa';
    } elseif (preg_match('/^5[1-5]/', $card_number)) {
        return 'Mastercard';
    } elseif (preg_match('/^3[47]/', $card_number)) {
        return 'American Express';
    } elseif (preg_match('/^6(?:011|5)/', $card_number)) {
        return 'Discover';
    } elseif (preg_match('/^(?:2131|1800|35)/', $card_number)) {
        return 'JCB';
    } else {
        return 'Credit/Debit Card';
    }
}
?>
