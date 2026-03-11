<?php
require_once 'config.php';

$conn->query("SET time_zone = '+05:30'");
date_default_timezone_set('Asia/Kolkata');

$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';
$error = '';
$success = '';
$valid_token = false;
$user = null;

if (!empty($token)) {
    $stmt = $conn->prepare("SELECT user_id, 
                            full_name, 
                            email FROM users 
                            WHERE reset_token = ? 
                            AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $valid_token = true;
        $user = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt2->bind_param("ss", $hashed_password, $token);
        if ($stmt2->execute()) {
            $success = "Password reset successful! You can now login with your new password.";
            $valid_token = false;
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Intzi</title>
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

        .form-group { margin-bottom: 1.5rem; }

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
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:hover { background: var(--secondary-blue); }

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

        .password-strength {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .strength-weak { color: #ef4444; }
        .strength-medium { color: #f59e0b; }
        .strength-strong { color: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-lock"></i>
            <h1>Reset Password</h1>
            <p style="color: var(--text-light); margin-top: 0.5rem;">Create a new password for your account</p>
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
            <a href="login.php" class="btn">
                <i class="fas fa-sign-in-alt"></i> Go to Login
            </a>
        <?php elseif($valid_token): ?>
            <div style="background: #dbeafe; color: #1e40af; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <i class="fas fa-user"></i> Resetting password for: <strong><?php echo htmlspecialchars($user['email']); ?></strong>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" name="password" id="password" required 
                           placeholder="Enter new password (min 6 characters)"
                           minlength="6">
                    <div class="password-strength" id="strength"></div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required 
                           placeholder="Re-enter new password">
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-check"></i> Reset Password
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> Invalid or expired reset link.
                <div style="margin-top: 1rem; font-size: 0.9rem;">
                    This could happen if:
                    <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                        <li>The link has expired (valid for 1 hour)</li>
                        <li>The link was already used</li>
                        <li>The link is incorrect</li>
                    </ul>
                </div>
            </div>
            <a href="forgot-password.php" class="btn">
                <i class="fas fa-redo"></i> Request New Reset Link
            </a>
        <?php endif; ?>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const strengthDiv = document.getElementById('strength');

        if(passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if(password.length >= 6) strength++;
                if(password.length >= 10) strength++;
                if(/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if(/\d/.test(password)) strength++;
                if(/[^a-zA-Z\d]/.test(password)) strength++;
                
                if(password.length === 0) {
                    strengthDiv.textContent = '';
                } else if(strength <= 2) {
                    strengthDiv.className = 'password-strength strength-weak';
                    strengthDiv.textContent = '⚠️ Weak password';
                } else if(strength <= 3) {
                    strengthDiv.className = 'password-strength strength-medium';
                    strengthDiv.textContent = '⚡ Medium password';
                } else {
                    strengthDiv.className = 'password-strength strength-strong';
                    strengthDiv.textContent = '✅ Strong password';
                }
            });

            // Password match check
            confirmInput.addEventListener('input', function() {
                if(this.value === passwordInput.value && this.value.length > 0) {
                    this.style.borderColor = '#10b981';
                } else if(this.value.length > 0) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#e5e7eb';
                }
            });
        }
    </script>
</body>
</html>
