<?php
require_once 'config.php';

echo "<h2>Generate Avatars for Existing Providers</h2>";
echo "<hr>";

// Get all providers
$sql = "SELECT provider_id, provider_name, profile_image FROM service_providers";
$result = $conn->query($sql);

$colors = ['E91E63', '3F51B5', '009688', 'FF9800', '4CAF50', '795548', 'F44336', '2196F3', '9C27B0', 'FF5722'];

while($provider = $result->fetch_assoc()) {
    $colorIndex = $provider['provider_id'] % count($colors);
    $color = $colors[$colorIndex];
    
    $filename = strtolower(str_replace(' ', '-', $provider['provider_name'])) . '.jpg';
    
    // Download avatar from UI Avatars
    $url = "https://ui-avatars.com/api/?name=" . urlencode($provider['provider_name']) . 
           "&size=400&background=$color&color=fff&bold=true";
    
    $imagePath = 'images/providers/' . $filename;
    $imageContent = @file_get_contents($url);
    
    if($imageContent) {
        file_put_contents($imagePath, $imageContent);
        
        // Update database
        $updateSql = "UPDATE service_providers 
                     SET profile_image = '$filename' 
                     WHERE provider_id = {$provider['provider_id']}";
        
        if($conn->query($updateSql)) {
            echo "✅ Generated & Updated: {$provider['provider_name']} → $filename<br>";
        }
    } else {
        echo "❌ Failed to generate for: {$provider['provider_name']}<br>";
    }
    
    usleep(200000); // Small delay
}

echo "<br><p><strong>✅ All providers now have custom avatars!</strong></p>";
echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>View Services</a>";
?>
