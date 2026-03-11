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
    $skills = sanitize($_POST['skills']);
    $availability_status = sanitize($_POST['availability_status']);

    // Address fields
    $house_number = sanitize($_POST['house_number']);
    $street = sanitize($_POST['street']);
    $area = sanitize($_POST['area']);
    $landmark = sanitize($_POST['landmark']);
    $pincode = sanitize($_POST['pincode']);
    
    // Handle profile image upload
    $profile_image = $provider['profile_image']; // Keep existing by default
    
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $filesize = $_FILES['profile_image']['size'];
        
        // Check file size (max 5MB)
        if($filesize > 5 * 1024 * 1024) {
            $error = "Image size must be less than 5MB";
        } elseif(in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $new_filename = 'provider-' . $provider_id . '-' . time() . '.' . strtolower($filetype);
            $upload_path = 'images/providers/' . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old image if not default
                if($provider['profile_image'] != 'default-provider.jpg' && 
                   !empty($provider['profile_image']) && 
                   file_exists('images/providers/' . $provider['profile_image'])) {
                    unlink('images/providers/' . $provider['profile_image']);
                }
                $profile_image = $new_filename;
            } else {
                $error = "Failed to upload image";
            }
        } else {
            $error = "Invalid image format. Only JPG, PNG, GIF allowed";
        }
    }
    
    if(empty($error)) {
        $sql = "UPDATE service_providers SET 
                provider_name = '$provider_name',
                phone = '$phone',
                bio = '$bio',
                experience_years = $experience_years,
                hourly_rate = $hourly_rate',
                skills = '$skills',
                availability_status = '$availability_status',
                house_number = '$house_number',
                street = '$street',
                area = '$area',
                landmark = '$landmark',
                pincode = '$pincode',
                profile_image = '$profile_image'
                WHERE provider_id = $provider_id";
        
        if($conn->query($sql)) {
            $success = "Profile updated successfully!";
            $_SESSION['provider_name'] = $provider_name;
            $provider = getProviderDetails($provider_id); // Refresh data
        } else {
            $error = "Update failed. Please try again.";
        }
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

        .current-image {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .current-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-blue);
        }

        .image-upload-container {
            border: 2px dashed var(--border);
            padding: 1.5rem;
            text-align: center;
            border-radius: 8px;
            background: var(--bg-light);
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-container:hover {
            border-color: var(--primary-blue);
            background: #eff6ff;
        }

        .image-upload-container input[type="file"] {
            display: none;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto;
            display: none;
            border-radius: 8px;
            border: 2px solid var(--primary-blue);
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

        .btn-primary:hover {
            background: var(--secondary-blue);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .alert-success { 
            background: #d1fae5; 
            color: #065f46; 
            border-color: var(--success);
        }
        
        .alert-error { 
            background: #fee2e2; 
            color: #991b1b; 
            border-color: var(--danger);
        }

        small {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
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
                <li><a href="provider-earnings.php"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">My Profile</h1>

            <div class="card">
                <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <!-- Profile Picture Section -->
                    <div class="form-group">
                        <label><i class="fas fa-camera"></i> Profile Picture</label>
                        
                        <?php if(!empty($provider['profile_image'])): ?>
                        <div class="current-image">
                            <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                                 alt="Current profile picture"
                                 onerror="this.src='images/providers/default-provider.jpg'">
                            <div>
                                <strong>Current Photo</strong>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 0.3rem;">
                                    <?php echo htmlspecialchars($provider['profile_image']); ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="image-upload-container" onclick="document.getElementById('profile_image').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--primary-blue);"></i>
                            <p style="margin-top: 0.5rem; font-weight: 500;">Click to upload new profile picture</p>
                            <small>JPG, PNG, GIF (Max 5MB)</small>
                            <img id="preview" class="preview-image" alt="Preview">
                        </div>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                        <small style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Leave empty to keep current image
                        </small>
                    </div>

                    <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">

                    <!-- Basic Information -->
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Name / Business Name</label>
                        <input type="text" name="provider_name" value="<?php echo htmlspecialchars($provider['provider_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email (Cannot be changed)</label>
                        <input type="email" value="<?php echo htmlspecialchars($provider['email']); ?>" disabled style="background: #f3f4f6; cursor: not-allowed;">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($provider['phone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-briefcase"></i> Category (Cannot be changed)</label>
                        <input type="text" value="<?php echo htmlspecialchars($provider['category_name']); ?>" disabled style="background: #f3f4f6; cursor: not-allowed;">
                    </div>

                    <!-- Experience & Rates -->
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Years of Experience</label>
                        <input type="number" name="experience_years" value="<?php echo $provider['experience_years']; ?>" min="0" required>
                    </div>

                    <!-- Skills & Bio -->
                    <div class="form-group">
                        <label><i class="fas fa-tools"></i> Skills (comma-separated)</label>
                        <input type="text" name="skills" value="<?php echo htmlspecialchars($provider['skills']); ?>" required placeholder="e.g., Bridal Makeup, Hair Styling, Party Makeup">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Bio / About You</label>
                        <textarea name="bio" required placeholder="Tell customers about your experience and services..."><?php echo htmlspecialchars($provider['bio']); ?></textarea>
                        <small>A detailed bio helps customers understand your expertise</small>
                    </div>

                    <!-- Location -->
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($provider['address']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-city"></i> City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($provider['city']); ?>" required>
                    </div>

                    <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">
                    <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-map-marker-alt"></i> Service Area</h3>

                    <?php 
                    require_once 'hyderabad-data.php';
                    $address_data = [
                        'house_number' => $provider['house_number'],
                        'street' => $provider['street'],
                        'area' => $provider['area'],
                        'landmark' => $provider['landmark'],
                        'pincode' => $provider['pincode']
                    ];
                    include 'components/address-form.php'; 
                    ?>

                    <!-- Availability -->
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Availability Status</label>
                        <select name="availability_status" required>
                            <option value="available" <?php echo $provider['availability_status'] == 'available' ? 'selected' : ''; ?>>
                                ✅ Available for Bookings
                            </option>
                            <option value="busy" <?php echo $provider['availability_status'] == 'busy' ? 'selected' : ''; ?>>
                                ⏳ Busy (Limited Availability)
                            </option>
                            <option value="unavailable" <?php echo $provider['availability_status'] == 'unavailable' ? 'selected' : ''; ?>>
                                ❌ Unavailable (Not Taking Bookings)
                            </option>
                        </select>
                        <small>Update this based on your current workload</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if(file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if(!allowedTypes.includes(file.type)) {
                    alert('Only JPG, PNG, and GIF images are allowed');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
