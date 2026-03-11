<?php 
require_once 'config.php';
requireAdminLogin(); // Make sure you have this function in config.php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider_id = (int)$_POST['provider_id'];
    $suspension_reason = $conn->real_escape_string($_POST['suspension_reason']);
    $action_required = $conn->real_escape_string($_POST['action_required']);
    
    // Update provider status
    $sql = "UPDATE service_providers 
            SET account_status = 'suspended',
                suspension_reason = '$suspension_reason',
                suspension_date = NOW()
            WHERE provider_id = $provider_id";
    
    if($conn->query($sql)) {
        // Create notification for provider
        $notification_sql = "INSERT INTO provider_notifications 
                            (provider_id, notification_type, title, message, action_required, created_at) 
                            VALUES 
                            ($provider_id, 'suspension', 
                             'Account Suspended', 
                             'Your account has been suspended. Reason: $suspension_reason',
                             '$action_required',
                             NOW())";
        
        $conn->query($notification_sql);
        
        // Send email notification (optional)
        $provider_sql = "SELECT email, provider_name FROM service_providers WHERE provider_id = $provider_id";
        $provider_result = $conn->query($provider_sql);
        $provider = $provider_result->fetch_assoc();
        
        // Email sending code here (optional)
        /*
        $to = $provider['email'];
        $subject = "Intzi Account Suspension Notice";
        $message = "Dear {$provider['provider_name']},\n\n";
        $message .= "Your account has been suspended.\n\n";
        $message .= "Reason: $suspension_reason\n\n";
        $message .= "Next Steps: $action_required\n\n";
        $message .= "Please log in to your provider dashboard for more details.\n\n";
        $message .= "Best regards,\nIntzi Team";
        
        mail($to, $subject, $message);
        */
        
        $_SESSION['admin_success'] = "Provider suspended successfully and notification sent.";
        header("Location: admin-providers.php");
        exit();
    }
}
?>
