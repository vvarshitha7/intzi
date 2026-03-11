<?php 
require_once 'config.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'provider-dashboard.php';

if(isProviderLoggedIn()) {
    header("Location: " . $redirect);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $sql = "SELECT * FROM service_providers WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0) {
            $provider = $result->fetch_assoc();
            if(password_verify($password, $provider['password'])) {
                if($provider['account_status'] == 'pending') {
                    $error = "Your account is pending approval. Please wait for admin verification.";
                } elseif($provider['account_status'] == 'suspended') {
                    $error = "Your account has been suspended. Please contact support.";
                } else {
                    $_SESSION['provider_id'] = $provider['provider_id'];
                    $_SESSION['provider_name'] = $provider['provider_name'];
                    $_SESSION['provider_email'] = $provider['email'];
                    header("Location: " . $redirect);
                    exit();
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Login - Intzi</title>
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
            --border: #e5e7eb;
            --danger: #ef4444;
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

        .login-container {
            max-width: 500px;
            width: 100%;
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header i {
            font-size: 4rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .login-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .btn {
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--secondary-blue);
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-tie"></i>
            <h1>Provider Login</h1>
            <p style="color: var(--text-light);">Access your provider dashboard</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" required placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>

            <div style="text-align: right; margin-bottom: 1rem;">
                 <a href="provider-forgot-password.php" style="color: var(--primary-blue); font-size: 0.9rem;">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="form-footer">
            New provider? <a href="provider-register.php">Register here</a><br>
            <a href="index.php">Back to Customer Site</a>
        </div>
    </div>
</body>
</html>
