<?php
require_once 'config.php';

echo "<h2>Intzi Complete Demo Setup</h2>";
echo "<hr>";

// Step 1: Create folders
echo "<h3>Step 1: Creating Folders</h3>";
$folders = ['images', 'images/providers'];
foreach($folders as $folder) {
    if(!file_exists($folder)) {
        mkdir($folder, 0777, true);
        echo "✅ Created: $folder<br>";
    } else {
        echo "ℹ️ Already exists: $folder<br>";
    }
}

// Step 2: Create images
echo "<br><h3>Step 2: Creating Provider Images</h3>";

function createProviderImage($filename, $name, $bgColor) {
    $img = imagecreatetruecolor(400, 400);
    $r = hexdec(substr($bgColor, 0, 2));
    $g = hexdec(substr($bgColor, 2, 2));
    $b = hexdec(substr($bgColor, 4, 2));
    $background = imagecolorallocate($img, $r, $g, $b);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, 400, 400, $background);
    
    $initials = '';
    $words = explode(' ', $name);
    foreach($words as $word) {
        $initials .= strtoupper($word[0]);
    }
    
    imagestring($img, 5, 160, 180, $initials, $white);
    imagestring($img, 3, 100, 220, $name, $white);
    
    imagejpeg($img, 'images/providers/' . $filename, 90);
    imagedestroy($img);
}

// Create default image
$img = imagecreatetruecolor(400, 400);
$blue = imagecolorallocate($img, 37, 99, 235);
$white = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, 400, 400, $blue);
imagestring($img, 5, 80, 190, "DEFAULT PROVIDER", $white);
imagejpeg($img, 'images/providers/default-provider.jpg', 90);
imagedestroy($img);
echo "✅ Created: default-provider.jpg<br>";

// Create provider images
$imageData = [
    ['priya-sharma.jpg', 'Priya Sharma', 'E91E63'],
    ['rajesh-kumar.jpg', 'Rajesh Kumar', '3F51B5'],
    ['anita-verma.jpg', 'Anita Verma', '009688'],
    ['arjun-singh.jpg', 'Arjun Singh', 'FF9800'],
    ['meera-patel.jpg', 'Meera Patel', '4CAF50'],
    ['vikram-reddy.jpg', 'Vikram Reddy', '795548'],
    ['sneha-iyer.jpg', 'Sneha Iyer', 'F44336'],
    ['amit-gupta.jpg', 'Amit Gupta', '2196F3'],
    ['pooja-nair.jpg', 'Pooja Nair', '9C27B0'],
    ['rahul-mehta.jpg', 'Rahul Mehta', 'FF5722']
];

foreach($imageData as $data) {
    createProviderImage($data[0], $data[1], $data[2]);
    echo "✅ Created: {$data[0]}<br>";
}

// Step 3: Insert providers
echo "<br><h3>Step 3: Adding Sample Providers to Database</h3>";

$providers = [
    ['Priya Sharma', 'priya@example.com', '9876543210', 1, 'Expert tailor with 15 years of experience', 15, 300, 'Bridal Wear, Alterations', 'Hyderabad', 'priya-sharma.jpg', 4.8],
    ['Rajesh Kumar', 'rajesh@example.com', '9876543211', 2, 'Professional makeup artist and hair stylist', 10, 500, 'Bridal Makeup, Hair Styling', 'Hyderabad', 'rajesh-kumar.jpg', 4.9],
    ['Anita Verma', 'anita@example.com', '9876543212', 3, 'Expert cook specializing in North Indian cuisine', 12, 400, 'North Indian, Party Catering', 'Hyderabad', 'anita-verma.jpg', 4.7],
    ['Arjun Singh', 'arjun@example.com', '9876543213', 4, 'Professional house cleaning services', 8, 200, 'Deep Cleaning, Maintenance', 'Hyderabad', 'arjun-singh.jpg', 4.6],
    ['Meera Patel', 'meera@example.com', '9876543214', 1, 'Modern fashion designer', 7, 350, 'Fashion Design, Embroidery', 'Hyderabad', 'meera-patel.jpg', 4.5],
    ['Vikram Reddy', 'vikram@example.com', '9876543215', 3, 'Traditional South Indian chef', 20, 450, 'South Indian, Catering', 'Hyderabad', 'vikram-reddy.jpg', 4.9],
    ['Sneha Iyer', 'sneha@example.com', '9876543216', 2, 'Beauty specialist', 6, 350, 'Facial, Waxing, Threading', 'Hyderabad', 'sneha-iyer.jpg', 4.7],
    ['Amit Gupta', 'amit@example.com', '9876543217', 4, 'Household work expert', 10, 250, 'Cooking, Cleaning, Laundry', 'Hyderabad', 'amit-gupta.jpg', 4.6],
    ['Pooja Nair', 'pooja@example.com', '9876543218', 1, 'Kids wear specialist', 5, 280, 'Kids Wear, Ethnic Clothing', 'Hyderabad', 'pooja-nair.jpg', 4.5],
    ['Rahul Mehta', 'rahul@example.com', '9876543219', 3, 'Multi-cuisine chef', 14, 500, 'Chinese, Italian, Indian', 'Hyderabad', 'rahul-mehta.jpg', 4.8]
];

$password = password_hash('password123', PASSWORD_DEFAULT);

foreach($providers as $p) {
    $sql = "INSERT INTO service_providers 
            (provider_name, email, password, phone, category_id, bio, experience_years, 
             hourly_rate, skills, city, profile_image, rating, account_status, availability_status) 
            VALUES 
            ('$p[0]', '$p[1]', '$password', '$p[2]', $p[3], '$p[4]', $p[5], $p[6], '$p[7]', '$p[8]', '$p[9]', $p[10], 'active', 'available')";
    
    if($conn->query($sql)) {
        echo "✅ Added: $p[0]<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

echo "<br><h3>🎉 Setup Complete!</h3>";
echo "<p><strong>Default password for all providers:</strong> password123</p>";
echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View Services</a>";
echo "<a href='index.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Go to Homepage</a>";
?>
