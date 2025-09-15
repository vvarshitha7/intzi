<?php
session_start();
require_once 'config.php'; // Database connection

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? '');  // Using email here
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
    $error = 'Please enter both email and password.';
} else {
    // Check Users table first
    $stmt = $conn->prepare("SELECT id, full_name, password FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $full_name, $password_hash);
        $stmt->fetch();
        if (password_verify($password, $password_hash)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $full_name;
            $_SESSION['role'] = 'user';
            header('Location: homepage.html');
            exit;
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $stmt->close();
        // Check ServiceProviders table
        $stmt = $conn->prepare("SELECT id, full_name, password FROM ServiceProviders WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $full_name, $password_hash);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['userid'] = $id;
                $_SESSION['username'] = $full_name;
                $_SESSION['role'] = 'provider';
                header('Location: vendorhomepage.php'); 
                exit;
            } else {
                $error = 'Incorrect password.';
            }
        } else {
            $error = 'Email not found.';
        }
        $stmt->close();
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | INTZI</title>
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
    .login-container {
      background: #fff;
      border-radius: 24px;
      padding: 40px 36px;
      box-shadow: 0 8px 28px rgba(95, 100, 236, 0.15);
      max-width: 360px;
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
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border-radius: 10px;
      border: 1.7px solid #d1d7fe;
      font-size: 1rem;
      transition: border-color 0.3s;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #5f64ec;
      outline: none;
    }
    .error {
      color: #e74c3c;
      font-weight: 700;
      margin-bottom: 20px;
      text-align: center;
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
    .links {
      margin-top: 18px;
      text-align: center;
    }
    .links a {
      color: #5f64ec;
      text-decoration: none;
      margin: 0 10px;
      font-weight: 600;
    }
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container" role="main" aria-label="Login form">
    <h2>Login</h2>
    <?php if ($error): ?>
      <div class="error" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <label for="username">Email</label>
      <input type="text" id="username" name="username" required autocomplete="email" />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password" />

      <button type="submit" aria-label="Login">Log In</button>
    </form>

    <div class="links">
      <a href="forgot_password.php" aria-label="Forgot Password">Forgot Password?</a>
      |
      <a href="signup.php" aria-label="Register Account">Register</a>
    </div>
  </div>
</body>
</html>