<?php
require_once 'config.php';

echo "<h2>Recalculating Provider Ratings</h2>";
echo "<hr>";

// Get all providers
$providers_sql = "SELECT provider_id, provider_name FROM service_providers";
$providers_result = $conn->query($providers_sql);

$updated = 0;
$no_reviews = 0;

while($provider = $providers_result->fetch_assoc()) {
    $provider_id = $provider['provider_id'];
    
    // Calculate rating from reviews
    $rating_sql = "SELECT 
                  COUNT(*) as total_reviews,
                  COALESCE(AVG(rating), 0) as average_rating
                  FROM reviews 
                  WHERE provider_id = $provider_id";
    
    $rating_result = $conn->query($rating_sql);
    $rating_data = $rating_result->fetch_assoc();
    
    $total_reviews = $rating_data['total_reviews'];
    $average_rating = $total_reviews > 0 ? round($rating_data['average_rating'], 1) : 0;
    
    // Update provider
    $update_sql = "UPDATE service_providers 
                  SET rating = $average_rating,
                      total_reviews = $total_reviews
                  WHERE provider_id = $provider_id";
    
    if($conn->query($update_sql)) {
        if($total_reviews > 0) {
            echo "✅ {$provider['provider_name']}: $average_rating stars ($total_reviews reviews)<br>";
            $updated++;
        } else {
            echo "ℹ️ {$provider['provider_name']}: No reviews yet (rating set to 0)<br>";
            $no_reviews++;
        }
    }
}

echo "<br><h3>Summary:</h3>";
echo "✅ Updated $updated providers with reviews<br>";
echo "ℹ️ $no_reviews providers have no reviews yet<br>";
echo "<br><a href='services.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>View Services</a>";
?>
