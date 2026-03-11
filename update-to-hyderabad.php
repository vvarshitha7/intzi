<?php
require_once 'config.php';
require_once 'hyderabad-data.php';

echo "<h2>Updating All Data to Hyderabad</h2>";
echo "<hr>";

// Sample Hyderabad addresses for existing users/providers
$sample_addresses = [
    ['house' => '12-3-456', 'street' => 'MG Road', 'area' => 'Banjara Hills', 'landmark' => 'Near Apollo Hospital', 'pincode' => '500034'],
    ['house' => '8-2-293/82/A/765', 'street' => 'Road No 12', 'area' => 'Jubilee Hills', 'landmark' => 'Opposite GVK One Mall', 'pincode' => '500033'],
    ['house' => 'Flat 301', 'street' => 'Hitec City Main Road', 'area' => 'Madhapur', 'landmark' => 'Near Inorbit Mall', 'pincode' => '500081'],
    ['house' => '15-4-789', 'street' => 'Ameerpet Main Road', 'area' => 'Ameerpet', 'landmark' => 'Near Metro Station', 'pincode' => '500016'],
    ['house' => 'H.No 4-5-123', 'street' => 'LB Nagar Main Road', 'area' => 'LB Nagar', 'landmark' => 'Near Metro Station', 'pincode' => '500074'],
    ['house' => '10-2-567', 'street' => 'Kukatpally Main Road', 'area' => 'Kukatpally', 'landmark' => 'Near KPHB Junction', 'pincode' => '500072'],
    ['house' => 'Plot 234', 'street' => 'Gachibowli Main Road', 'area' => 'Gachibowli', 'landmark' => 'Near DLF Cyber City', 'pincode' => '500032'],
    ['house' => '7-1-234', 'street' => 'Dilsukhnagar Main Road', 'area' => 'Dilsukhnagar', 'landmark' => 'Near Bus Stop', 'pincode' => '500060'],
    ['house' => '5-9-876', 'street' => 'Secunderabad Railway Station Road', 'area' => 'Secunderabad', 'landmark' => 'Near Clock Tower', 'pincode' => '500003'],
    ['house' => '11-6-543', 'street' => 'Kondapur Main Road', 'area' => 'Kondapur', 'landmark' => 'Near IKEA', 'pincode' => '500084'],
];

// Update users
echo "<h3>Updating Users...</h3>";
$users = $conn->query("SELECT user_id FROM users");
$index = 0;
while($user = $users->fetch_assoc()) {
    $addr = $sample_addresses[$index % count($sample_addresses)];
    $sql = "UPDATE users SET 
            house_number = '{$addr['house']}',
            street = '{$addr['street']}',
            area = '{$addr['area']}',
            landmark = '{$addr['landmark']}',
            pincode = '{$addr['pincode']}',
            city = 'Hyderabad',
            state = 'Telangana'
            WHERE user_id = {$user['user_id']}";
    
    if($conn->query($sql)) {
        echo "✅ Updated User ID: {$user['user_id']} - {$addr['area']}<br>";
    }
    $index++;
}

// Update service providers
echo "<br><h3>Updating Service Providers...</h3>";
$providers = $conn->query("SELECT provider_id, provider_name FROM service_providers");
$index = 0;
while($provider = $providers->fetch_assoc()) {
    $addr = $sample_addresses[$index % count($sample_addresses)];
    $sql = "UPDATE service_providers SET 
            house_number = '{$addr['house']}',
            street = '{$addr['street']}',
            area = '{$addr['area']}',
            landmark = '{$addr['landmark']}',
            pincode = '{$addr['pincode']}',
            city = 'Hyderabad',
            state = 'Telangana'
            WHERE provider_id = {$provider['provider_id']}";
    
    if($conn->query($sql)) {
        echo "✅ Updated Provider: {$provider['provider_name']} - {$addr['area']}<br>";
    }
    $index++;
}

// Update existing bookings
echo "<br><h3>Updating Existing Bookings...</h3>";
$bookings = $conn->query("SELECT booking_id FROM bookings");
$index = 0;
while($booking = $bookings->fetch_assoc()) {
    $addr = $sample_addresses[$index % count($sample_addresses)];
    $sql = "UPDATE bookings SET 
            house_number = '{$addr['house']}',
            street = '{$addr['street']}',
            area = '{$addr['area']}',
            landmark = '{$addr['landmark']}',
            pincode = '{$addr['pincode']}'
            WHERE booking_id = {$booking['booking_id']}";
    
    if($conn->query($sql)) {
        echo "✅ Updated Booking ID: {$booking['booking_id']}<br>";
    }
    $index++;
}

echo "<br><h3>✅ Update Complete!</h3>";
echo "<p>All data has been updated to Hyderabad addresses.</p>";
echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>View Services</a>";
?>
