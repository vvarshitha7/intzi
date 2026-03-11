<?php 
require_once 'config.php';
requireLogin();

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: services.php");
    exit();
}

$booking_id = (int)$_POST['booking_id'];
$amount = (float)$_POST['amount'];
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Validate payment method
$valid_methods = ['card', 'upi', 'netbanking'];
if(!in_array($payment_method, $valid_methods)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit();
}

// Simulate payment processing
$transaction_id = 'TXN' . time() . rand(1000, 9999);
$payment_status = 'completed'; // In real scenario, this comes from payment gateway

// Update booking payment status
$update_sql = "UPDATE bookings 
               SET payment_status = 'paid',
                   payment_method = '$payment_method',
                   transaction_id = '$transaction_id',
                   paid_at = NOW()
               WHERE booking_id = $booking_id AND user_id = {$_SESSION['user_id']}";

if($conn->query($update_sql)) {
    // Redirect to success page
    header("Location: payment-success.php?booking_id=$booking_id&transaction_id=$transaction_id");
    exit();
} else {
    header("Location: payment-failed.php?booking_id=$booking_id");
    exit();
}
?>
