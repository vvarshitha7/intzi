<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch($action) {
    case 'update_provider_status':
        $provider_id = (int)$_POST['provider_id'];
        $status = $_POST['status'];
        
        // Validate status
        if(!in_array($status, ['pending', 'active', 'suspended'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit();
        }
        
        $sql = "UPDATE service_providers SET account_status = ? WHERE provider_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $status, $provider_id);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'delete_provider':
        $provider_id = (int)$_POST['provider_id'];
        
        // Delete related bookings first (foreign key constraint)
        $conn->query("DELETE FROM bookings WHERE provider_id = $provider_id");
        
        // Delete reviews
        $conn->query("DELETE FROM reviews WHERE provider_id = $provider_id");
        
        // Delete provider
        $sql = "DELETE FROM service_providers WHERE provider_id = $provider_id";
        
        if($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'delete_user':
        $user_id = (int)$_POST['user_id'];
        
        // Delete user's bookings first
        $conn->query("DELETE FROM bookings WHERE user_id = $user_id");
        
        // Delete user's reviews
        $conn->query("DELETE FROM reviews WHERE user_id = $user_id");
        
        // Delete user
        $sql = "DELETE FROM users WHERE user_id = $user_id";
        
        if($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
