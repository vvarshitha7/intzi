<?php 
require_once 'config.php';

$error = '';
$success = '';

if(isProviderLoggedIn()) {
    header("Location: provider-dashboard.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider_name = sanitize($_POST['provider_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $category_id = (int)$_POST['category_id'];
    $bio = sanitize($_POST['bio']);
    $experience_years = (int)$_POST['experience_years'];
    $hourly_rate = (float)$_POST['hourly_rate'];
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $skills = sanitize($_POST['skills']);
    
    if(empty($provider_name) || empty($email) || empty($phone) || empty($password)) {
        $error = "Please fill in all required fields";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $check_sql = "SELECT * FROM service_providers WHERE email = '$email'";
        $check_result = $conn->query($check_sql);
        
        if($check_result->num_rows > 0) {
            $error = "Email already registered. Please login.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO service_providers (provider_name, email, phone, password, category_id, bio, experience_years, hourly_rate, address, city, skills, account_status, profile_image) 
                    VALUES ('$provider_name', '$email', '$phone', '$hashed_password', $category_id, '$bio', $experience_years, $hourly_rate, '$address', '$city', '$skills', 'pending', 'default-provider.jpg')";
            
            if($conn->query($sql)) {
                $success = "Registration successful! Your account is pending approval. You'll be notified via email once approved.";
                // In a real app, send email notification to admin
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// Fetch categories
$categories_query = "SELECT * FROM service_categories";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Registration - Intzi</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            max-width: 900px;
            width: 100%;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .register-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .register-content {
            padding: 2rem;
        }

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
            color: var(--text-dark);
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
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
            width: 100%;
        }

        .btn-primary:hover {
            background: var(--secondary-blue);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
        }

        .form-footer a {
            color: var(--primary-blue);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-user-tie"></i> Become a Service Provider</h1>
            <p>Join Intzi and start earning by offering your services</p>
        </div>

        <div class="register-content">
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php else: ?>
            
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name / Business Name *</label>
                        <input type="text" name="provider_name" required placeholder="Enter your name">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" name="email" required placeholder="your@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number *</label>
                        <input type="tel" name="phone" required placeholder="Enter phone number">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-briefcase"></i> Service Category *</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password *</label>
                        <input type="password" name="password" required placeholder="Min 6 characters">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password *</label>
                        <input type="password" name="confirm_password" required placeholder="Re-enter password">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Years of Experience *</label>
                        <input type="number" name="experience_years" min="0" max="50" required placeholder="Years">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-rupee-sign"></i> Hourly Rate (₹) *</label>
                        <input type="number" name="hourly_rate" min="100" step="50" required placeholder="Rate per hour">
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-tools"></i> Skills (comma-separated) *</label>
                        <input type="text" name="skills" required placeholder="e.g., Tailoring, Alterations, Custom Design">
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-info-circle"></i> About You / Bio *</label>
                        <textarea name="bio" required placeholder="Tell customers about your experience and expertise"></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-map-marker-alt"></i> Service Area Address *</label>
                        <input type="text" name="address" required placeholder="Your service location">
                    </div>
                    
                    <div class="form-group full-width">
                        <label><i class="fas fa-city"></i> City *</label>
                        <input type="text" name="city" required placeholder="City name">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Register as Service Provider
                </button>
            </form>
            
            <?php endif; ?>
            
            <div class="form-footer">
                Already have an account? <a href="provider-login.php">Login here</a> | 
                <a href="index.php">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
