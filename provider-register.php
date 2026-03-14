<?php 
require_once 'config.php';
require_once 'hyderabad-data.php';


$error = '';
$success = '';


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider_name = sanitize($_POST['provider_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $phone = sanitize($_POST['phone']);
    $category_id = (int)$_POST['category_id'];
    $bio = sanitize($_POST['bio']);
    $experience = (int)$_POST['experience_years'];
    $skills = sanitize($_POST['skills']);
    
    // Address fields
    $house_number = sanitize($_POST['house_number']);
    $street = sanitize($_POST['street']);
    $area = sanitize($_POST['area']);
    $landmark = sanitize($_POST['landmark']);
    $pincode = sanitize($_POST['pincode']);
    $city = 'Hyderabad';
    $state = 'Telangana';
    
    // Handle profile image upload
    $profile_image = 'default-provider.jpg';
    
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = strtolower(str_replace(' ', '-', $provider_name)) . '-' . time() . '.' . $filetype;
            $upload_path = 'images/providers/' . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image = $new_filename;
            }
        }
    }
    
    // Check if email exists
    $check_sql = "SELECT provider_id FROM service_providers WHERE email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if($check_result->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO service_providers 
                (provider_name, email, password, phone, category_id, bio, experience_years, 
                 skills, house_number, street, area, landmark, pincode, city, state,
                 profile_image, account_status, availability_status) 
                VALUES 
                ('$provider_name', '$email', '$hashed_password', '$phone', $category_id, '$bio', 
                 $experience, '$skills', '$house_number', '$street', '$area', 
                 '$landmark', '$pincode', '$city', '$state', '$profile_image', 'pending', 'available')";
        
        if($conn->query($sql)) {
            $success = "Registration successful! Your account is pending admin approval.";
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}


// Get categories for dropdown
$categories = $conn->query("SELECT * FROM service_categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Registration - Intzi Hyderabad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .header p { color: var(--text-light); }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
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
            padding: 0.9rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .image-upload {
            border: 2px dashed var(--border);
            padding: 2rem;
            text-align: center;
            border-radius: 8px;
            background: var(--bg-light);
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload:hover {
            border-color: var(--primary-blue);
            background: #eff6ff;
        }

        .image-upload input[type="file"] {
            display: none;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto;
            display: none;
            border-radius: 8px;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            background: var(--primary-blue);
            color: white;
            transition: all 0.3s;
        }

        .btn:hover {
            background: #1e40af;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        small {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-user-tie" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
            <h1>Service Provider Registration</h1>
            <p>Join Intzi - Hyderabad's #1 Service Marketplace</p>
        </div>

        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <a href="provider-login.php" class="btn">Go to Login</a>
        <?php else: ?>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name / Business Name *</label>
                    <input type="text" name="provider_name" required placeholder="Your name or business name">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password *</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" required
                               minlength="8" placeholder="Min 8 characters"
                               style="padding-right: 3rem;">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon')"
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
                                       background: none; border: none; cursor: pointer;
                                       color: var(--text-light); font-size: 1.1rem; padding: 0;">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone *</label>
                    <input type="tel" name="phone" required placeholder="10-digit number" pattern="[0-9]{10}">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Service Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Years of Experience *</label>
                    <input type="number" name="experience_years" required min="0" placeholder="Years">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tools"></i> Skills *</label>
                    <input type="text" name="skills" required placeholder="e.g., Bridal Makeup, Hair Styling">
                </div>

                <div class="form-group full-width">
                    <label><i class="fas fa-align-left"></i> Bio / About Your Services *</label>
                    <textarea name="bio" required rows="4" placeholder="Tell customers about yourself and your services..."></textarea>
                </div>
            </div>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">

            <?php 
            $address_data = []; // Empty for new registration
            include 'components/address-form.php'; 
            ?>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">

            <div class="form-group full-width">
                <label><i class="fas fa-camera"></i> Profile Picture (Optional)</label>
                <div class="image-upload" onclick="document.getElementById('profile_image').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary-blue);"></i>
                    <p style="margin-top: 0.5rem;">Click to upload profile picture</p>
                    <small>JPG, PNG, GIF (Max 5MB)</small>
                    <img id="preview" class="preview-image" alt="Preview">
                </div>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <button type="submit" class="btn" style="margin-top: 1.5rem;">
                <i class="fas fa-user-plus"></i> Register as Provider
            </button>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: var(--text-light);">
                    Already registered? 
                    <a href="provider-login.php" style="color: var(--primary-blue); font-weight: 600;">Login here</a>
                </p>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        function updatePincode(selectedArea) {
            const areaSelect = document.getElementById('area');
            const pincodeInput = document.getElementById('pincode');
            const selectedOption = areaSelect.options[areaSelect.selectedIndex];
            
            if(selectedOption && selectedOption.dataset.pincode) {
                pincodeInput.value = selectedOption.dataset.pincode;
            }
        }
    </script>
</body>
</html>
