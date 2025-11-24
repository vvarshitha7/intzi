<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];
$provider = getProviderDetails($provider_id);

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider_name = sanitize($_POST['provider_name']);
    $phone = sanitize($_POST['phone']);
    $bio = sanitize($_POST['bio']);
    $experience_years = (int)$_POST['experience_years'];
    $hourly_rate = (float)$_POST['hourly_rate'];
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $skills = sanitize($_POST['skills']);
    $availability_status = sanitize($_POST['availability_status']);
    
    $sql = "UPDATE service_providers SET 
            provider_name = '$provider_name',
            phone = '$phone',
            bio = '$bio',
            experience_years = $experience_years,
            hourly_rate = $hourly_rate,
            address = '$address',
            city = '$city',
            skills = '$skills',
            availability_status = '$availability_status'
            WHERE provider_id = $provider_id";
    
    if($conn->query($sql)) {
        $success = "Profile updated successfully!";
        $_SESSION['provider_name'] = $provider_name;
        $provider = getProviderDetails($provider_id); // Refresh data
    } else {
        $error = "Update failed. Please try again.";
    }
}

$categories_query = "SELECT * FROM service_categories";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Provider Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Same base styles */
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
            --danger: #ef4444;
            --border: #e5e7eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
            width: 100%;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-hands-helping"></i> Intzi</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="provider-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="provider-bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">My Profile</h1>

            <div class="card">
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Name / Business Name</label>
                        <input type="text" name="provider_name" value="<?php echo htmlspecialchars($provider['provider_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email (Cannot be changed)</label>
                        <input type="email" value="<?php echo htmlspecialchars($provider['email']); ?>" disabled style="background: #f3f4f6;">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($provider['phone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Category (Cannot be changed)</label>
                        <input type="text" value="<?php echo htmlspecialchars($provider['category_name']); ?>" disabled style="background: #f3f4f6;">
                    </div>

                    <div class="form-group">
                        <label>Years of Experience</label>
                        <input type="number" name="experience_years" value="<?php echo $provider['experience_years']; ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Hourly Rate (₹)</label>
                        <input type="number" name="hourly_rate" value="<?php echo $provider['hourly_rate']; ?>" min="100" step="50" required>
                    </div>

                    <div class="form-group">
                        <label>Skills (comma-separated)</label>
                        <input type="text" name="skills" value="<?php echo htmlspecialchars($provider['skills']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Bio / About</label>
                        <textarea name="bio" required><?php echo htmlspecialchars($provider['bio']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($provider['address']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($provider['city']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Availability Status</label>
                        <select name="availability_status" required>
                            <option value="available" <?php echo $provider['availability_status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="busy" <?php echo $provider['availability_status'] == 'busy' ? 'selected' : ''; ?>>Busy</option>
                            <option value="unavailable" <?php echo $provider['availability_status'] == 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
