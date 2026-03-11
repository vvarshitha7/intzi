<?php
// Payment Configuration for Demo
// This is a simplified payment system for demonstration

function processDemoPayment($booking_id, $amount, $payment_method, $payment_details) {
    global $conn;
    
    // Generate transaction ID
    $transaction_id = 'TXN' . strtoupper(uniqid());
    $payment_date = date('Y-m-d H:i:s');
    
    // Escape values for security
    $payment_method = $conn->real_escape_string($payment_method);
    
    // Update booking with payment info
    $update_sql = "UPDATE bookings SET 
                   payment_status = 'paid',
                   payment_method = '$payment_method',
                   transaction_id = '$transaction_id',
                   payment_date = '$payment_date',
                   booking_status = 'confirmed'
                   WHERE booking_id = $booking_id";
    
    if($conn->query($update_sql)) {
        // Get provider ID and service charge from booking
        $booking = $conn->query("SELECT provider_id, total_amount FROM bookings WHERE booking_id = $booking_id")->fetch_assoc();
        
        if($booking) {
            // Update provider earnings
            $provider_id = $booking['provider_id'];
            $service_charge = $booking['total_amount'];
            
            $earnings_sql = "UPDATE service_providers 
                           SET total_earnings = total_earnings + $service_charge,
                               total_bookings = total_bookings + 1
                           WHERE provider_id = $provider_id";
            $conn->query($earnings_sql);
        }
        
        return [
            'success' => true,
            'transaction_id' => $transaction_id,
            'payment_date' => $payment_date
        ];
    } else {
        return [
            'success' => false,
            'error' => $conn->error
        ];
    }
}

function validatePaymentDetails($payment_method, $payment_details) {
    switch($payment_method) {
        case 'card':
            // Validate card details
            if(empty($payment_details['card_number']) || 
               empty($payment_details['card_holder']) || 
               empty($payment_details['expiry']) || 
               empty($payment_details['cvv'])) {
                return ['valid' => false, 'message' => 'Please fill all card details'];
            }
            
            // Basic card number validation (16 digits)
            if(strlen(str_replace(' ', '', $payment_details['card_number'])) < 15) {
                return ['valid' => false, 'message' => 'Invalid card number'];
            }
            
            // CVV validation (3 digits)
            if(strlen($payment_details['cvv']) != 3) {
                return ['valid' => false, 'message' => 'Invalid CVV'];
            }
            
            return ['valid' => true];
            break;
            
        case 'upi':
            // Validate UPI ID
            if(empty($payment_details['upi_id'])) {
                return ['valid' => false, 'message' => 'Please enter UPI ID'];
            }
            
            if(strpos($payment_details['upi_id'], '@') === false) {
                return ['valid' => false, 'message' => 'Invalid UPI ID format'];
            }
            
            return ['valid' => true];
            break;
            
        case 'netbanking':
            // Validate bank selection
            if(empty($payment_details['bank']) || $payment_details['bank'] == '') {
                return ['valid' => false, 'message' => 'Please select a bank'];
            }
            
            return ['valid' => true];
            break;
            
        default:
            return ['valid' => false, 'message' => 'Invalid payment method'];
    }
}

// List of supported banks for net banking
$supported_banks = [
    'SBI' => 'State Bank of India',
    'HDFC' => 'HDFC Bank',
    'ICICI' => 'ICICI Bank',
    'Axis' => 'Axis Bank',
    'Kotak' => 'Kotak Mahindra Bank',
    'PNB' => 'Punjab National Bank',
    'BOB' => 'Bank of Baroda',
    'Canara' => 'Canara Bank',
    'Union' => 'Union Bank of India',
    'IndusInd' => 'IndusInd Bank'
];
?>
