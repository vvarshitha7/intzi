<?php
require_once 'config.php';
requireProviderLogin();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = (int)$_POST['booking_id'];
    $status = sanitize($_POST['status']);
    $provider_id = $_SESSION['provider_id'];
    
    // Verify this booking belongs to the logged-in provider
    $verify_sql = "SELECT * FROM bookings WHERE booking_id = $booking_id AND provider_id = $provider_id";
    $verify_result = $conn->query($verify_sql);
    
    if($verify_result->num_rows > 0) {
        $update_sql = "UPDATE bookings SET booking_status = '$status' WHERE booking_id = $booking_id";
        
        if($conn->query($update_sql)) {
            $_SESSION['success_message'] = "Booking status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update booking status.";
        }
    } else {
        $_SESSION['error_message'] = "Unauthorized action.";
    }
}

header("Location: provider-bookings.php");
exit();
?>
