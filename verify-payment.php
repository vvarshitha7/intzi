<?php
require_once 'config.php';
require_once 'payment-config.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$razorpay_order_id = $input['razorpay_order_id'];
$razorpay_payment_id = $input['razorpay_payment_id'];
$razorpay_signature = $input['razorpay_signature'];
$booking_id = (int)$input['booking_id'];

// Verify signature
$verified = verifyRazorpaySignature($razorpay_order_id, $razorpay_payment_id, $razorpay_signature);

if($verified) {
    // Fetch payment details from Razorpay
    $payment = getRazorpayPayment($razorpay_payment_id);
    
    if($payment && $payment['status'] === 'captured') {
        // Update booking
        $sql = "UPDATE bookings SET 
                razorpay_payment_id = '$razorpay_payment_id',
                razorpay_signature = '$razorpay_signature',
                payment_status = 'completed',
                payment_method = '{$payment['method']}',
                payment_date = NOW(),
                booking_status = 'confirmed'
                WHERE booking_id = $booking_id";
        
        if($conn->query($sql)) {
            // Insert into payments table
            $user_id = $_SESSION['user_id'];
            $amount = $payment['amount'] / 100;
            
            $payment_sql = "INSERT INTO payments (booking_id, user_id, razorpay_order_id, razorpay_payment_id, razorpay_signature, amount, status, payment_method) 
                           VALUES ($booking_id, $user_id, '$razorpay_order_id', '$razorpay_payment_id', '$razorpay_signature', $amount, 'captured', '{$payment['method']}')";
            $conn->query($payment_sql);
            
            // Clear session
            unset($_SESSION['booking_id']);
            unset($_SESSION['razorpay_order_id']);
            unset($_SESSION['payment_amount']);
            
            echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment not captured']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid signature']);
}
?>
