<?php 
require_once 'config.php';
require_once 'hyderabad-data.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get user details
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    // Address fields
    $house_number = sanitize($_POST['house_number']);
    $street = sanitize($_POST['street']);
    $area = sanitize($_POST['area']);
    $landmark = sanitize($_POST['landmark']);
    $pincode = sanitize($_POST['pincode']);
    
    $update_sql = "UPDATE users SET 
                  full_name = '$full_name',
                  phone = '$phone',
                  house_number = '$house_number',
                  street = '$street',
                  area = '$area',
                  landmark = '$landmark',
                  pincode = '$pincode'
                  WHERE user_id = $user_id";
    
    if($conn->query($update_sql)) {
        $success = "Profile updated successfully!";
        $_SESSION['user_name'] = $full_name;
        // Refresh user data
        $user_result = $conn->query($user_sql);
        $user = $user_result->fetch_assoc();
    } else {
        $error = "Update failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Intzi Hyderabad</title>
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
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-blue);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dark);
            font-weight: 500;
            text-decoration: none;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
            width: 100%;
        }

        .btn-outline {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 5%;
        }

        .profile-card {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-hands-helping"></i> Intzi
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="my-bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="profile-card">
            <h1 style="margin-bottom: 2rem; text-align: center;">My Profile</h1>

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

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email (Cannot be changed)</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled 
                               style="background: #f3f4f6; cursor: not-allowed;">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                           required pattern="[0-9]{10}">
                </div>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">

                <?php 
                $address_data = [
                    'house_number' => $user['house_number'],
                    'street' => $user['street'],
                    'area' => $user['area'],
                    'landmark' => $user['landmark'],
                    'pincode' => $user['pincode']
                ];
                include 'components/address-form.php'; 
                ?>

                <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
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
