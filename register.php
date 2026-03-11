<?php 
require_once 'config.php';
require_once 'hyderabad-data.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize($_POST['phone']);
    
    // Address fields
    $house_number = sanitize($_POST['house_number']);
    $street = sanitize($_POST['street']);
    $area = sanitize($_POST['area']);
    $landmark = sanitize($_POST['landmark']);
    $pincode = sanitize($_POST['pincode']);
    $city = 'Hyderabad';
    $state = 'Telangana';
    
    if($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if email exists
        $check_email = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
        
        if($check_email->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users 
                    (full_name, email, password, phone, house_number, street, area, 
                     landmark, pincode, city, state) 
                    VALUES 
                    ('$full_name', '$email', '$hashed_password', '$phone', '$house_number', 
                     '$street', '$area', '$landmark', '$pincode', '$city', '$state')";
            
            if($conn->query($sql)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Intzi Hyderabad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
            --danger: #ef4444;
            --success: #10b981;
            --border: #e5e7eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .register-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn:hover {
            background: #1e40af;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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

        small {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
            <h1>Create Account</h1>
            <p style="color: var(--text-light);">Join Intzi - Hyderabad's Service Marketplace</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <a href="login.php" class="btn">Go to Login</a>
        <?php else: ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name *</label>
                    <input type="text" name="full_name" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="Min 8 characters">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password *</label>
                    <input type="password" name="confirm_password" required placeholder="Re-enter password">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-phone"></i> Phone Number *</label>
                <input type="tel" name="phone" required placeholder="10-digit mobile number" pattern="[0-9]{10}">
            </div>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">

            <?php 
            $address_data = []; // Empty for new registration
            include 'components/address-form.php'; 
            ?>

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Register
            </button>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: var(--text-light);">
                    Already have an account? 
                    <a href="login.php" style="color: var(--primary-blue); font-weight: 600;">Login here</a>
                </p>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
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
