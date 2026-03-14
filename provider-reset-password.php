<?php 
require_once 'config.php';


$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';
$error = '';
$success = '';
$valid_token = false;


if(!empty($token)) {
    $sql = "SELECT provider_id, provider_name, email FROM service_providers 
            WHERE reset_token = '$token' 
            AND reset_token_expiry > NOW()";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $valid_token = true;
        $provider = $result->fetch_assoc();
    } else {
        $error = "Invalid or expired reset link.";
    }
}


if($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE service_providers SET 
                      password = '$hashed_password',
                      reset_token = NULL,
                      reset_token_expiry = NULL
                      WHERE reset_token = '$token'";
        
        if($conn->query($update_sql)) {
            $success = "Password reset successful! You can now login with your new password.";
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
    <title>Provider Reset Password - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --text-dark: #1f2937;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-user-lock"></i>
            <h1>Reset Provider Password</h1>
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
            <a href="provider-login.php" class="btn">
                <i class="fas fa-sign-in-alt"></i> Go to Provider Login
            </a>
        <?php elseif($valid_token): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" required
                               placeholder="Enter new password (min 8 characters)"
                               minlength="8" style="padding-right: 3rem;">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')"
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
                                       background: none; border: none; cursor: pointer;
                                       color: #6b7280; font-size: 1.1rem; padding: 0;">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" id="confirm_password" required
                               placeholder="Re-enter new password" style="padding-right: 3rem;">
                        <button type="button" onclick="togglePassword('confirm_password', 'toggleIcon2')"
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
                                       background: none; border: none; cursor: pointer;
                                       color: #6b7280; font-size: 1.1rem; padding: 0;">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-check"></i> Reset Password
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> Invalid or expired reset link.
            </div>
            <a href="provider-forgot-password.php" class="btn">
                <i class="fas fa-redo"></i> Request New Link
            </a>
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
    </script>
</body>
</html>
