<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (!$token) {
    die('Invalid or missing token.');
}

// Check token validity
$stmt = $conn->prepare("SELECT id, email, expires_at FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    die('Invalid or expired token.');
}

$stmt->bind_result($user_id, $email, $expires_at);
$stmt->fetch();

if (strtotime($expires_at) < time()) {
    die('Token expired. Please request a new password reset.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$password || !$confirm_password) {
        $error = "Please fill both password fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Determine which table user is in
        $user_table = '';
        // Check Users table
        $user_check = $conn->prepare("SELECT id FROM Users WHERE email = ?");
        $user_check->bind_param("s", $email);
        $user_check->execute();
        $user_check->store_result();
        if ($user_check->num_rows === 1) {
            $user_table = 'Users';
        } else {
            $user_check->close();
            // check ServiceProviders table
            $provider_check = $conn->prepare("SELECT id FROM ServiceProviders WHERE email = ?");
            $provider_check->bind_param("s", $email);
            $provider_check->execute();
            $provider_check->store_result();
            if ($provider_check->num_rows === 1) {
                $user_table = 'ServiceProviders';
            }
            $provider_check->close();
        }

        if (!$user_table) {
            $error = "User not found.";
        } else {
            // Update password
            $update = $conn->prepare("UPDATE $user_table SET password = ? WHERE email = ?");
            $update->bind_param("ss", $password_hash, $email);
            if ($update->execute()) {
                // Delete used token
                $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $del->bind_param("s", $token);
                $del->execute();
                $success = "Password updated successfully! You can now <a href=\"login.php\">login</a>.";
            } else {
                $error = "Failed to update password.";
            }
            $update->close();
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password | INTZI</title>
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
input[type="password"] {
  width: 100%;
  padding: 10px 14px;
  margin-bottom: 20px;
  border-radius: 10px;
  border: 1.7px solid #d1d7fe;
  font-size: 1rem;
  transition: border-color 0.3s;
}
input[type="password"]:focus {
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
  <div class="container" role="main" aria-label="Password reset form">
    <h2>Reset Password</h2>
    <?php if ($error): ?>
      <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!$success): ?>
    <form method="POST" action="">
      <label for="password">New Password</label>
      <input type="password" id="password" name="password" required />

      <label for="confirm_password">Confirm New Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required />

      <button type="submit" aria-label="Reset password">Reset Password</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>