<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = ""; // set your password here
$dbname = "intzi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only process form when POST method is used
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $pwd = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $phone = $_POST['phone'] ?? '';
    $house = $_POST['house'] ?? '';
    $street = $_POST['street'] ?? '';
    $mandal = $_POST['mandal'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $pincode = $_POST['pincode'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($full_name) || empty($email) || empty($pwd) || empty($phone) || empty($mandal) || empty($city) || empty($state) || empty($pincode) || empty($role)) {
        echo "Please fill all required fields.";
        exit();
    }

    if ($role == 'user') {
        $stmt = $conn->prepare("INSERT INTO Users (full_name, email, password, phone, house, street, mandal, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $full_name, $email, $pwd, $phone, $house, $street, $mandal, $city, $state, $pincode);
    } elseif ($role == 'provider') {
        $service_category = $_POST['service_category'] ?? '';
        $portfolio = $_POST['portfolio'] ?? '';
        if (empty($service_category)) {
            echo "Please select a Service Category.";
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO ServiceProviders (full_name, email, password, phone, house, street, mandal, city, state, pincode, service_category, portfolio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $full_name, $email, $pwd, $phone, $house, $street, $mandal, $city, $state, $pincode, $service_category, $portfolio);
    } else {
        echo "Please select a role.";
        exit();
    }

    if ($stmt->execute()) {
        echo "Signup successful!";
        // Optionally redirect to login page: header("Location: login.html"); exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
