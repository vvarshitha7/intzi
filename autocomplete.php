<?php
require_once 'config.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

if(strlen($query) < 2) {
    echo json_encode(['providers' => [], 'skills' => [], 'categories' => []]);
    exit();
}

$response = [
    'providers' => [],
    'skills' => [],
    'categories' => []
];

// Get matching providers
$providers_sql = "SELECT DISTINCT provider_name 
                  FROM service_providers 
                  WHERE account_status = 'active' 
                  AND provider_name LIKE '%$query%' 
                  LIMIT 5";
$providers_result = $conn->query($providers_sql);
while($row = $providers_result->fetch_assoc()) {
    $response['providers'][] = ['name' => $row['provider_name']];
}

// Get matching skills
$skills_sql = "SELECT DISTINCT skills 
               FROM service_providers 
               WHERE account_status = 'active' 
               AND skills LIKE '%$query%'";
$skills_result = $conn->query($skills_sql);
$skills_set = [];
while($row = $skills_result->fetch_assoc()) {
    $skills_array = explode(',', $row['skills']);
    foreach($skills_array as $skill) {
        $skill = trim($skill);
        if(stripos($skill, $query) !== false && !in_array($skill, $skills_set)) {
            $skills_set[] = $skill;
            if(count($skills_set) >= 5) break 2;
        }
    }
}
$response['skills'] = $skills_set;

// Get matching categories
$categories_sql = "SELECT DISTINCT category_name 
                   FROM service_categories 
                   WHERE category_name LIKE '%$query%' 
                   LIMIT 5";
$categories_result = $conn->query($categories_sql);
while($row = $categories_result->fetch_assoc()) {
    $response['categories'][] = $row['category_name'];
}

echo json_encode($response);
?>
