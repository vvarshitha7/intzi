<?php
$host = 'localhost';
$user = 'root';
$pass = '';    // Change if necessary
$dbname = 'intzi';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
