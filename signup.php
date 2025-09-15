<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $house = trim($_POST['house'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $mandal = trim($_POST['mandal'] ?? '');
    $city = trim($_POST['city'] ?? 'Hyderabad');
    $state = trim($_POST['state'] ?? 'Telangana');
    $pincode = trim($_POST['pincode'] ?? '');
    $role = $_POST['role'] ?? '';

    if (!$full_name || !$email || !$password || !$confirm_password || !$phone || !$mandal || !$city || !$state || !$pincode || !$role) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if ($role === 'user') {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Email already in use.";
            } else {
                $stmt->close();
                $stmt = $conn->prepare("INSERT INTO Users (full_name, email, password, phone, house, street, mandal, city, state, pincode)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssss", $full_name, $email, $password_hash, $phone, $house, $street, $mandal, $city, $state, $pincode);
                if ($stmt->execute()) {
                    $success = "User registration successful! You can now <a href='login.php'>login</a>.";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
            }
            $stmt->close();
        } elseif ($role === 'provider') {
            $service_category = trim($_POST['service_category'] ?? '');
            $portfolio = trim($_POST['portfolio'] ?? '');
            if (!$service_category) {
                $error = "Please select a Service Category.";
            } else {
                // Check if email exists
                $stmt = $conn->prepare("SELECT id FROM ServiceProviders WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error = "Email already in use.";
                } else {
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO ServiceProviders (full_name, email, password, phone, house, street, mandal, city, state, pincode, service_category, portfolio)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssssssss", $full_name, $email, $password_hash, $phone, $house, $street, $mandal, $city, $state, $pincode, $service_category, $portfolio);
                    if ($stmt->execute()) {
                        $success = "Provider registration successful! You can now <a href='login.php'>login</a>.";
                    } else {
                        $error = "Database error: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
        } else {
            $error = "Please select a valid role.";
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
  <title>Register | INTZI</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    /* Same styling as previous registration form */
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #f7f8fc;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 10px;
    }
    .container {
      background: #fff;
      border-radius: 24px;
      padding: 40px 36px;
      box-shadow: 0 8px 28px rgba(95, 100, 236, 0.15);
      max-width: 480px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
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
    input[type="email"],
    input[type="password"],
    select,
    textarea {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border-radius: 10px;
      border: 1.7px solid #d1d7fe;
      font-size: 1rem;
      transition: border-color 0.3s;
      resize: vertical;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus,
    textarea:focus {
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
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
  <script>
    // Show/hide vendor fields based on role selection
    function roleChanged() {
      const role = document.getElementById('role').value;
      document.getElementById('providerFields').style.display = (role === 'provider') ? 'block' : 'none';
    }
    window.onload = roleChanged;
  </script>
</head>
<body>
  <div class="container" role="main" aria-label="User and Vendor registration form">
    <h2>Register</h2>

    <?php if ($error): ?>
      <div class="message error"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required />

      <label for="phone">Phone Number</label>
      <input type="text" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" />

      <label for="house">House/Apartment</label>
      <input type="text" id="house" name="house" value="<?php echo htmlspecialchars($_POST['house'] ?? ''); ?>" />

      <label for="street">Street</label>
      <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($_POST['street'] ?? ''); ?>" />

      <label for="mandal">Mandal</label>
      <input type="text" id="mandal" name="mandal" required value="<?php echo htmlspecialchars($_POST['mandal'] ?? ''); ?>" />

      <label for="city">City</label>
      <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($_POST['city'] ?? 'Hyderabad'); ?>" />

      <label for="state">State</label>
      <input type="text" id="state" name="state" required value="<?php echo htmlspecialchars($_POST['state'] ?? 'Telangana'); ?>" />

      <label for="pincode">Pincode</label>
      <input type="text" id="pincode" name="pincode" required value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>" />

      <label for="role">Register As</label>
      <select id="role" name="role" onchange="roleChanged()" required>
        <option value="" disabled selected>Select role</option>
        <option value="user" <?php if (($_POST['role'] ?? '') === 'user') echo 'selected'; ?>>User</option>
        <option value="provider" <?php if (($_POST['role'] ?? '') === 'provider') echo 'selected'; ?>>Service Provider</option>
      </select>

      <div id="providerFields" style="display:none;">
  <label for="service_category">Service Category</label>
  <select id="service_category" name="service_category" required>
    <option value="" disabled <?php if (empty($_POST['service_category'])) echo 'selected'; ?>>Select category</option>
    <option value="tailoring" <?php if (($_POST['service_category'] ?? '') === 'tailoring') echo 'selected'; ?>>Tailoring</option>
    <option value="mehndi artistry" <?php if (($_POST['service_category'] ?? '') === 'mehndi artistry') echo 'selected'; ?>>Mehndi Artistry</option>
    <option value="beauty treatments" <?php if (($_POST['service_category'] ?? '') === 'beauty treatments') echo 'selected'; ?>>Beauty Treatments</option>
    <option value="food or catering" <?php if (($_POST['service_category'] ?? '') === 'food or catering') echo 'selected'; ?>>Food or Catering</option>
    <option value="household labor" <?php if (($_POST['service_category'] ?? '') === 'household labor') echo 'selected'; ?>>Household Labor</option>
  </select>

  <label for="portfolio">Portfolio (URLs, description)</label>
  <textarea id="portfolio" name="portfolio"><?php echo htmlspecialchars($_POST['portfolio'] ?? ''); ?></textarea>
</div>

      <button type="submit" aria-label="Register">Register</button>
    </form>

    <p style="text-align:center; margin-top: 16px;">Already have an account? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
