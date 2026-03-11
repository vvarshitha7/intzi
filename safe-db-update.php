<?php
require_once 'config.php';

echo "<h2>Safe Database Update for Hyderabad System</h2>";
echo "<hr>";

// Function to check if column exists
function columnExists($table, $column) {
    global $conn;
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Function to add column safely
function addColumnSafe($table, $column, $definition, $after = null) {
    global $conn;
    if (!columnExists($table, $column)) {
        $sql = "ALTER TABLE $table ADD COLUMN $column $definition";
        if ($after) {
            $sql .= " AFTER $after";
        }
        if ($conn->query($sql)) {
            echo "✅ Added '$column' to '$table'<br>";
            return true;
        } else {
            echo "❌ Error adding '$column' to '$table': " . $conn->error . "<br>";
            return false;
        }
    } else {
        echo "ℹ️ Column '$column' already exists in '$table'<br>";
        return true;
    }
}

// Function to drop column safely
function dropColumnSafe($table, $column) {
    global $conn;
    if (columnExists($table, $column)) {
        $sql = "ALTER TABLE $table DROP COLUMN $column";
        if ($conn->query($sql)) {
            echo "✅ Dropped old column '$column' from '$table'<br>";
            return true;
        } else {
            echo "❌ Error dropping '$column' from '$table': " . $conn->error . "<br>";
            return false;
        }
    } else {
        echo "ℹ️ Column '$column' doesn't exist in '$table' (already removed)<br>";
        return true;
    }
}

echo "<h3>Step 1: Updating 'users' Table</h3>";
addColumnSafe('users', 'house_number', 'VARCHAR(50)', 'phone');
addColumnSafe('users', 'street', 'VARCHAR(100)', 'house_number');
addColumnSafe('users', 'area', 'VARCHAR(100)', 'street');
addColumnSafe('users', 'landmark', 'VARCHAR(100)', 'area');
addColumnSafe('users', 'pincode', 'VARCHAR(6)', 'landmark');
addColumnSafe('users', 'city', "VARCHAR(50) DEFAULT 'Hyderabad'", 'pincode');
addColumnSafe('users', 'state', "VARCHAR(50) DEFAULT 'Telangana'", 'city');
dropColumnSafe('users', 'address');

echo "<br><h3>Step 2: Updating 'service_providers' Table</h3>";
addColumnSafe('service_providers', 'house_number', 'VARCHAR(50)', 'skills');
addColumnSafe('service_providers', 'street', 'VARCHAR(100)', 'house_number');
addColumnSafe('service_providers', 'area', 'VARCHAR(100)', 'street');
addColumnSafe('service_providers', 'landmark', 'VARCHAR(100)', 'area');
addColumnSafe('service_providers', 'pincode', 'VARCHAR(6)', 'landmark');
addColumnSafe('service_providers', 'city', "VARCHAR(50) DEFAULT 'Hyderabad'", 'pincode');
addColumnSafe('service_providers', 'state', "VARCHAR(50) DEFAULT 'Telangana'", 'city');
dropColumnSafe('service_providers', 'address');

echo "<br><h3>Step 3: Updating 'bookings' Table</h3>";
addColumnSafe('bookings', 'house_number', 'VARCHAR(50)', 'special_requests');
addColumnSafe('bookings', 'street', 'VARCHAR(100)', 'house_number');
addColumnSafe('bookings', 'area', 'VARCHAR(100)', 'street');
addColumnSafe('bookings', 'landmark', 'VARCHAR(100)', 'area');
addColumnSafe('bookings', 'pincode', 'VARCHAR(6)', 'landmark');
addColumnSafe('bookings', 'use_registered_address', 'BOOLEAN DEFAULT FALSE', 'pincode');
dropColumnSafe('bookings', 'user_address');

echo "<br><h3>Step 4: Creating Indexes</h3>";
$indexes = [
    "CREATE INDEX idx_user_pincode ON users(pincode)",
    "CREATE INDEX idx_provider_pincode ON service_providers(pincode)",
    "CREATE INDEX idx_user_area ON users(area)",
    "CREATE INDEX idx_provider_area ON service_providers(area)"
];

foreach($indexes as $index_sql) {
    if($conn->query($index_sql)) {
        echo "✅ Index created successfully<br>";
    } else {
        // Ignore duplicate key errors
        if(strpos($conn->error, 'Duplicate key name') !== false) {
            echo "ℹ️ Index already exists<br>";
        } else {
            echo "❌ Error: " . $conn->error . "<br>";
        }
    }
}

echo "<br><h3>🎉 Database Update Complete!</h3>";
echo "<p>Your database is now ready for the Hyderabad address system.</p>";
echo "<br><a href='register.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Test Registration</a>";
echo " ";
echo "<a href='services.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>View Services</a>";
?>
