<?php
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intzi_db');

// Create Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Define base paths
define('BASE_URL', 'http://localhost/intzi_db/');

// ============================================
// USER FUNCTIONS
// ============================================

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to redirect to login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

// ============================================
// PROVIDER FUNCTIONS
// ============================================

// Helper function to check if provider is logged in
function isProviderLoggedIn() {
    return isset($_SESSION['provider_id']);
}

// Helper function to require provider login
function requireProviderLogin() {
    if (!isProviderLoggedIn()) {
        header("Location: provider-login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

// Helper function to get provider details
function getProviderDetails($provider_id) {
    global $conn;
    $sql = "SELECT sp.*, sc.category_name 
            FROM service_providers sp 
            LEFT JOIN service_categories sc ON sp.category_id = sc.category_id 
            WHERE sp.provider_id = $provider_id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// ============================================
// ADMIN FUNCTIONS
// ============================================

// Helper function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Helper function to require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: admin-login.php");
        exit();
    }
}

// ============================================
// GENERAL HELPER FUNCTIONS
// ============================================

// Helper function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Helper function to generate transaction ID
function generateTransactionId() {
    return 'TXN' . time() . rand(1000, 9999);
}

// Helper function to format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Helper function to format time
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

// Helper function to get user IP
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper function to set flash message
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Helper function to get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>
