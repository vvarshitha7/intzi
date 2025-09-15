<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email exists in users or providers
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

                // Generate and store reset token with expiry (1 hour)
                $token = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', time() + 3600);

                // Create password_resets table if not existing first (one-time setup)
                $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    token VARCHAR(64) NOT NULL,
                    expires_at DATETIME NOT NULL
                )");

                // Remove any existing tokens for this email
                $conn->query("DELETE FROM password_resets WHERE email = '$email'");

                // Insert new token
                $insert = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)");
                $insert->bind_param("isss", $id, $email, $token, $expiry);
                $insert->execute();

                // For demo: display reset link instead of emailing
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/intzi-hackathon/reset_password.php?token=$token";
                $message = 'Password reset link (valid 1 hour): <a href="' . htmlspecialchars($reset_link) . '">reset</a>';
                $found = true;
                break;
            }
            $stmt->close();
        }
        if (!$found) {
            $error = "Email address not found.";
        }
    }
}
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
.success {
  color: #18c799;
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
    <?php endif; ?>
    <form method="POST" action="">
      <label for="email">Enter your registered Email</label>
      <input type="email" id="email" name="email" required autofocus autocomplete="email" />
      <button type="submit" aria-label="Send password reset link">Send Reset Link</button>
    </form>
  </div>
</body>
</html>