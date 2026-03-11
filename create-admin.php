<?php
require_once 'config.php';

// Create admin table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if($conn->query($create_table)) {
    echo "✅ Admin table created/exists<br>";
} else {
    echo "❌ Error creating table: " . $conn->error . "<br>";
}

// Delete existing admin if any
$conn->query("DELETE FROM admins WHERE username = 'admin'");

// Create new admin
$password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO admins (username, email, password, full_name, role) 
        VALUES ('admin', 'admin@intzi.com', '$password', 'System Administrator', 'super_admin')";

if($conn->query($sql)) {
    echo "✅ Admin account created successfully!<br><br>";
    echo "<strong>Login Credentials:</strong><br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br><br>";
    echo "<a href='admin-login.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a>";
} else {
    echo "❌ Error creating admin: " . $conn->error;
}
?>
