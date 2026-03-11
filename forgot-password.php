<?php
require_once 'config.php';

$conn->query("SET time_zone = '+05:30'");
date_default_timezone_set('Asia/Kolkata');

$success = '';
$error = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);

    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));

            // Store expiry as MySQL's future time (for timezone match!)
            $stmt2 = $conn->prepare("UPDATE users SET 
                                    reset_token = ?, 
                                    reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                                    WHERE email = ?");
            $stmt2->bind_param("ss", $token, $email);
            
            if ($stmt2->execute()) {
                $reset_link = BASE_URL . "reset-password.php?token=$token";
                $success = "Password reset link has been generated successfully!";
            } else {
                $error = "Database error. Please try again.";
            }
        } else {
            // Do not reveal if account exists
            $success = "If this email is registered, you will receive a password reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Intzi</title>
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
            max-width: 550px;
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

        .reset-link-box {
            background: #f3f4f6;
            border: 2px dashed var(--primary-blue);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .reset-link-box a {
            color: var(--primary-blue);
            word-break: break-all;
            font-size: 0.9rem;
        }

        .copy-btn {
            background: var(--success);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            margin-top: 0.5rem;
            font-family: 'Poppins', sans-serif;
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

        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-key"></i>
            <h1>Forgot Password?</h1>
            <p>Enter your email to receive a password reset link</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success && $reset_link): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            
            <div class="reset-link-box">
                <strong style="display: block; margin-bottom: 0.5rem;">
                    <i class="fas fa-link"></i> Your Password Reset Link:
                </strong>
                <a href="<?php echo $reset_link; ?>" id="resetLink"><?php echo $reset_link; ?></a>
                <br>
                <button class="copy-btn" onclick="copyLink()">
                    <i class="fas fa-copy"></i> Copy Link
                </button>
                <div style="margin-top: 1rem; padding: 0.8rem; background: #fff3cd; border-radius: 6px; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> This link will expire in 1 hour. In production, this would be sent to your email.
                </div>
            </div>

            <a href="<?php echo $reset_link; ?>" class="btn" style="text-decoration: none; display: block;">
                <i class="fas fa-arrow-right"></i> Go to Reset Password Page
            </a>
        <?php elseif($success): ?>
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
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <script>
        function copyLink() {
            const link = document.getElementById('resetLink').textContent;
            navigator.clipboard.writeText(link).then(function() {
                alert('Reset link copied to clipboard!');
            });
        }
    </script>
</body>
</html>
