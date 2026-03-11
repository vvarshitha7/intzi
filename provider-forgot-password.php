<?php 
require_once 'config.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    if(empty($email)) {
        $error = "Please enter your email address";
    } else {
        $sql = "SELECT provider_id, provider_name FROM service_providers WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0) {
            $provider = $result->fetch_assoc();
            
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $update_sql = "UPDATE service_providers SET 
                          reset_token = '$token', 
                          reset_token_expiry = '$expiry' 
                          WHERE email = '$email'";
            
            if($conn->query($update_sql)) {
                $reset_link = BASE_URL . "provider-reset-password.php?token=$token";
                $success = "Password reset link generated! <br><br>
                           <strong>Copy this link:</strong><br>
                           <a href='$reset_link' style='color: #2563eb; word-break: break-all;'>$reset_link</a><br><br>
                           <small>(In production, this would be sent to your email)</small>";
            } else {
                $error = "Failed to generate reset link. Please try again.";
            }
        } else {
            $success = "If this email exists, you will receive a password reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Forgot Password - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --text-dark: #1f2937;
            --text-light: #6b7280;
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header i {
            font-size: 3.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .header h1 { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .header p { color: var(--text-light); font-size: 0.95rem; }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
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

        .btn:hover { background: var(--secondary-blue); }

        .alert {
            padding: 1rem;
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

        .links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-user-shield"></i>
            <h1>Provider Password Reset</h1>
            <p>Enter your registered email address</p>
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
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your registered email">
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
        <?php endif; ?>

        <div class="links">
            <a href="provider-login.php"><i class="fas fa-arrow-left"></i> Back to Provider Login</a>
        </div>
    </div>
</body>
</html>
