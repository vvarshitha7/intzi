<?php
require_once 'config.php';

echo "<h2>Intzi Image Setup (No GD Required)</h2>";
echo "<hr>";

// Create folders
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

echo "<br><h3>Step 2: Downloading Provider Images</h3>";

// Provider data with avatar URLs
$providers = [
    ['name' => 'Priya Sharma', 'filename' => 'priya-sharma.jpg', 'color' => 'E91E63'],
    ['name' => 'Rajesh Kumar', 'filename' => 'rajesh-kumar.jpg', 'color' => '3F51B5'],
    ['name' => 'Anita Verma', 'filename' => 'anita-verma.jpg', 'color' => '009688'],
    ['name' => 'Arjun Singh', 'filename' => 'arjun-singh.jpg', 'color' => 'FF9800'],
    ['name' => 'Meera Patel', 'filename' => 'meera-patel.jpg', 'color' => '4CAF50'],
    ['name' => 'Vikram Reddy', 'filename' => 'vikram-reddy.jpg', 'color' => '795548'],
    ['name' => 'Sneha Iyer', 'filename' => 'sneha-iyer.jpg', 'color' => 'F44336'],
    ['name' => 'Amit Gupta', 'filename' => 'amit-gupta.jpg', 'color' => '2196F3'],
    ['name' => 'Pooja Nair', 'filename' => 'pooja-nair.jpg', 'color' => '9C27B0'],
    ['name' => 'Rahul Mehta', 'filename' => 'rahul-mehta.jpg', 'color' => 'FF5722']
];

// Download default provider image
$defaultUrl = "https://ui-avatars.com/api/?name=Default+Provider&size=400&background=2563eb&color=fff&bold=true";
$defaultPath = 'images/providers/default-provider.jpg';

$imageContent = @file_get_contents($defaultUrl);
if($imageContent) {
    file_put_contents($defaultPath, $imageContent);
    echo "✅ Downloaded: default-provider.jpg<br>";
} else {
    echo "❌ Failed to download default image<br>";
}

// Download provider images
foreach($providers as $provider) {
    $url = "https://ui-avatars.com/api/?name=" . urlencode($provider['name']) . 
           "&size=400&background=" . $provider['color'] . "&color=fff&bold=true";
    $path = 'images/providers/' . $provider['filename'];
    
    $imageContent = @file_get_contents($url);
    if($imageContent) {
        file_put_contents($path, $imageContent);
        echo "✅ Downloaded: {$provider['filename']}<br>";
    } else {
        echo "❌ Failed to download: {$provider['filename']}<br>";
    }
    
    usleep(200000); // Small delay to avoid overwhelming the API
}

echo "<br><h3>Step 3: Adding Sample Providers to Database</h3>";

$providersData = [
    ['Priya Sharma', 'priya@example.com', '9876543210', 1, 'Expert tailor with 15 years of experience in designing and alterations', 15, 300, 'Bridal Wear, Alterations, Custom Design', 'Hyderabad', 'priya-sharma.jpg', 4.8],
    ['Rajesh Kumar', 'rajesh@example.com', '9876543211', 2, 'Professional makeup artist and hair stylist', 10, 500, 'Bridal Makeup, Hair Styling, Party Makeup', 'Hyderabad', 'rajesh-kumar.jpg', 4.9],
    ['Anita Verma', 'anita@example.com', '9876543212', 3, 'Expert cook specializing in North Indian and Continental cuisine', 12, 400, 'North Indian, Continental, Party Catering', 'Hyderabad', 'anita-verma.jpg', 4.7],
    ['Arjun Singh', 'arjun@example.com', '9876543213', 4, 'Professional house cleaning and maintenance services', 8, 200, 'Deep Cleaning, Regular Maintenance, Organizing', 'Hyderabad', 'arjun-singh.jpg', 4.6],
    ['Meera Patel', 'meera@example.com', '9876543214', 1, 'Modern fashion designer with expertise in contemporary wear', 7, 350, 'Fashion Design, Embroidery, Stitching', 'Hyderabad', 'meera-patel.jpg', 4.5],
    ['Vikram Reddy', 'vikram@example.com', '9876543215', 3, 'Traditional South Indian chef with expertise in authentic recipes', 20, 450, 'South Indian, Traditional Cooking, Catering', 'Hyderabad', 'vikram-reddy.jpg', 4.9],
    ['Sneha Iyer', 'sneha@example.com', '9876543216', 2, 'Beauty specialist offering facial treatments and grooming services', 6, 350, 'Facial, Waxing, Threading, Manicure', 'Hyderabad', 'sneha-iyer.jpg', 4.7],
    ['Amit Gupta', 'amit@example.com', '9876543217', 4, 'Experienced in all types of household work', 10, 250, 'Cooking, Cleaning, Laundry, Child Care', 'Hyderabad', 'amit-gupta.jpg', 4.6],
    ['Pooja Nair', 'pooja@example.com', '9876543218', 1, 'Specialized in kids wear and ethnic clothing', 5, 280, 'Kids Wear, Ethnic Clothing, Embroidery', 'Hyderabad', 'pooja-nair.jpg', 4.5],
    ['Rahul Mehta', 'rahul@example.com', '9876543219', 3, 'Multi-cuisine chef with expertise in Chinese, Italian, and Indian', 14, 500, 'Chinese, Italian, Indian, Fusion Cuisine', 'Hyderabad', 'rahul-mehta.jpg', 4.8]
];

$password = password_hash('password123', PASSWORD_DEFAULT);
$success = 0;

foreach($providersData as $p) {
    $sql = "INSERT INTO service_providers 
            (provider_name, email, password, phone, category_id, bio, experience_years, 
             hourly_rate, skills, city, profile_image, rating, account_status, availability_status) 
            VALUES 
            ('$p[0]', '$p[1]', '$password', '$p[2]', $p[3], '$p[4]', $p[5], $p[6], '$p[7]', '$p[8]', '$p[9]', $p[10], 'active', 'available')";
    
    if($conn->query($sql)) {
        echo "✅ Added: $p[0]<br>";
        $success++;
    } else {
        // Check if already exists
        if(strpos($conn->error, 'Duplicate entry') !== false) {
            echo "ℹ️ Already exists: $p[0]<br>";
        } else {
            echo "❌ Error adding $p[0]: " . $conn->error . "<br>";
        }
    }
}

echo "<br><h3>🎉 Setup Complete!</h3>";
echo "<p>✅ Created folders<br>";
echo "✅ Downloaded " . (count($providers) + 1) . " images<br>";
echo "✅ Added $success providers to database</p>";
echo "<p><strong>Default password for all providers:</strong> password123</p>";
echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View Services</a>";
echo "<a href='index.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Go to Homepage</a>";
?>
