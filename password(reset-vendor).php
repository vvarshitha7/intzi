<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Tables to search for email
        $tables = ['Users', 'ServiceProviders'];
        $found = false;
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id);
                $stmt->fetch();
                $found = true;

                // Ensure password_resets table exists
                $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    token VARCHAR(64) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    INDEX (token),
                    INDEX (email)
                )");

                // Delete existing tokens for this email
                $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $del->bind_param("s", $email);
                $del->execute();
                $del->close();

                // Generate new reset token
                $token = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour valid

                // Insert token
                $insert = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)");
                $insert->bind_param("isss", $id, $email, $token, $expiry);
                $insert->execute();
                $insert->close();

                // Show user the code to copy
                $message = <<<HTML
                <p style="color:#18c799; font-weight:bold; text-align:center;">
                  ✔️ Password reset code generated<br>
                  Please copy the code below and 
                  <a href="reset_password.php" style="color:#5f64ec;">go to Reset Password page</a> to reset your password.
                </p>
                <input readonly value="$token" style="width:100%; font-size:1.1em; padding:10px; border-radius:8px; border:1.5px solid #5f64ec; text-align:center; font-family: monospace;" />
HTML;
                break;
            }
            $stmt->close();
        }

        if (!$found) {
            $error = "Email address not found.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forgot Password | INTZI</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #f7f8fc;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      border-radius: 24px;
      padding: 40px 36px;
      box-shadow: 0 8px 28px rgba(95, 100, 236, 0.15);
      max-width: 400px;
      width: 90%;
    }
    h2 {
      text-align: center;
      color: #5f64ec;
      margin-bottom: 30px;
    }
    label {
      font-weight: 600;
      display: block;
      margin-bottom: 8px;
      color: #3637a5;
    }
    input[type="email"] {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border-radius: 10px;
      border: 1.7px solid #d1d7fe;
      font-size: 1rem;
      transition: border-color 0.3s;
    }
    input[type="email"]:focus {
      border-color: #5f64ec;
      outline: none;
    }
    .message {
      margin-bottom: 20px;
      font-weight: 700;
      text-align: center;
    }
    .error {
      color: #e74c3c;
    }
    button {
      background: #5f64ec;
      color: #fff;
      border: none;
      padding: 14px 24px;
      font-weight: 700;
      font-size: 1.1rem;
      border-radius: 14px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background: #3637a5;
    }
    a {
      color: #5f64ec;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container" role="main" aria-label="Forgot password form">
    <h2>Forgot Password</h2>

    <?php if ($error): ?>
      <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="message success"><?php echo $message; ?></div>
    <?php else: ?>
    <form method="POST" action="">
      <label for="email">Enter your registered Email</label>
      <input type="email" id="email" name="email" required autofocus autocomplete="email" />
      <button type="submit" aria-label="Send password reset code">Send Reset Code</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
