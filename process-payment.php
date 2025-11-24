<?php
require_once 'config.php';
require_once 'payment-config.php';
requireLogin();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = (int)$_POST['booking_id'];
    $amount = (float)$_POST['amount'];
    $payment_method = sanitize($_POST['payment_method']);
    
    // Get card number for transaction ID (if available)
    $card_number = isset($_POST['card_number']) ? $_POST['card_number'] : '';
    
    // Process the demo payment
    $result = processDemoPayment($booking_id, $amount, $payment_method, $card_number);
    
    if($result['success']) {
        // Payment successful
        $_SESSION['payment_success'] = true;
        $_SESSION['transaction_id'] = $result['transaction_id'];
        
        // Clear booking session data
        unset($_SESSION['booking_id']);
        unset($_SESSION['payment_amount']);
        unset($_SESSION['provider_name']);
        unset($_SESSION['category_name']);
        
        header("Location: payment-success.php?booking_id=$booking_id");
        exit();
    } else {
        // Payment failed
        $_SESSION['payment_error'] = $result['message'];
        header("Location: payment-failed.php?booking_id=$booking_id");
        exit();
    }
} else {
    header("Location: services.php");
    exit();
}
?>
