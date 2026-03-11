<?php
require_once 'config.php';
requireAdminLogin();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider_id = (int)$_POST['provider_id'];
    $status = sanitize($_POST['status']);
    
    $sql = "UPDATE service_providers SET account_status = '$status' WHERE provider_id = $provider_id";
    
    if($conn->query($sql)) {
        $_SESSION['admin_success'] = "Provider status updated successfully!";
        
        // In a real app, send email notification to the provider
        // notifyProvider($provider_id, $status);
    }
}

header("Location: admin-providers.php");
exit();
?>
