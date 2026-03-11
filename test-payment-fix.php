<?php
require_once 'config.php';

echo "<h2>Payment System Test</h2>";
echo "<hr>";

// Check if razorpay columns exist (they should NOT)
$columns = $conn->query("SHOW COLUMNS FROM bookings LIKE 'razorpay%'");

if($columns->num_rows > 0) {
    echo "<div style='background: #fee2e2; padding: 1rem; border-left: 4px solid #ef4444; margin: 1rem 0;'>";
    echo "❌ <strong>Found old Razorpay columns. Please run the SQL fix!</strong><br>";
    while($col = $columns->fetch_assoc()) {
        echo "- {$col['Field']}<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background: #d1fae5; padding: 1rem; border-left: 4px solid #10b981; margin: 1rem 0;'>";
    echo "✅ <strong>No Razorpay columns found - Good!</strong>";
    echo "</div>";
}

// Check payment columns
echo "<h3>Payment Columns Status:</h3>";
$payment_cols = ['payment_method', 'transaction_id', 'payment_date', 'payment_status'];

foreach($payment_cols as $col) {
    $check = $conn->query("SHOW COLUMNS FROM bookings LIKE '$col'");
    if($check->num_rows > 0) {
        $info = $check->fetch_assoc();
        echo "✅ <strong>$col</strong>: {$info['Type']}<br>";
    } else {
        echo "❌ <strong>$col</strong>: Missing!<br>";
    }
}

echo "<br><h3>Recent Bookings:</h3>";
$bookings = $conn->query("SELECT booking_id, user_id, payment_status, payment_method, transaction_id FROM bookings ORDER BY booking_id DESC LIMIT 5");

if($bookings->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>User</th><th>Payment Status</th><th>Method</th><th>Transaction ID</th></tr>";
    while($b = $bookings->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$b['booking_id']}</td>";
        echo "<td>{$b['user_id']}</td>";
        echo "<td>{$b['payment_status']}</td>";
        echo "<td>" . ($b['payment_method'] ?? 'N/A') . "</td>";
        echo "<td>" . ($b['transaction_id'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Go to Services</a>";
?>
