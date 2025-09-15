<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vendor Profile | INTZI</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0; padding: 0;
      background: #f7f8fc;
      font-family: 'Poppins', Arial, sans-serif;
      color: #222;
    }
    .navbar {
      background: #5f64ec;
      display: flex;
      align-items: center;
      padding: 0 48px;
      height: 60px;
      font-family: 'Poppins', sans-serif;
      user-select: none;
      box-shadow: 0 4px 12px rgba(95, 100, 236, 0.18);
      position: sticky; top: 0; z-index: 100;
    }
    .navbar .brand {
      color: #fff; font-weight: 700; font-size: 1.8rem;
      letter-spacing: 2.5px; flex-shrink: 0; text-transform: uppercase;
    }
    .navbar ul {
      list-style: none; display: flex;
      flex-grow: 1; justify-content: space-between;
      margin: 0 0 0 48px; padding: 0;
    }
    .navbar ul li {
      flex: 1; text-align: center;
    }
    .navbar ul li a {
      color: #c3c9f9; text-decoration: none;
      font-weight: 600; font-size: 1rem;
      padding: 18px 0; display: block;
      border-radius: 6px 6px 0 0;
      transition: background-color 0.3s, color 0.3s;
    }
    .navbar ul li a:hover {
      background-color: #3e47d3; color: #eef2ff;
    }
    .navbar ul li a.active {
      background-color: #eef2ff; color: #5f64ec; font-weight: 700;
    }
    .profile-container {
      max-width: 700px;
      margin: 48px auto 60px auto;
      background: #fff;
      box-shadow: 0 8px 28px rgba(95, 100, 236, 0.15);
      border-radius: 24px;
      padding: 40px 36px 50px 36px;
    }
    .profile-header {
      text-align: center;
      margin-bottom: 36px;
    }
    .profile-avatar {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 5px solid #5f64ec;
      object-fit: cover;
      margin-bottom: 16px;
    }
    .profile-name {
      font-size: 2.2rem;
      font-weight: 700;
      color: #5f64ec;
      letter-spacing: 0.03em;
    }
    .profile-title {
      font-size: 1rem;
      font-weight: 600;
      color: #4a5368;
      margin-top: 4px;
    }
    form {
      max-width: 500px;
      margin: 0 auto;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #3637a5;
    }
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    textarea {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border: 1.7px solid #d1d7fe;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Poppins', Arial, sans-serif;
      color: #222;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    textarea:focus {
      outline: none;
      border-color: #5f64ec;
    }
    textarea {
      resize: vertical;
      min-height: 100px;
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
      transition: background-color 0.3s ease;
      display: block;
      margin: 0 auto;
      width: 140px;
    }
    button:hover {
      background: #3637a5;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="brand">INTZI</div>
    <ul>
      <li><a href="vendorhomepage.php">Dashboard</a></li>
      <li><a href="vendorservices.php">My Services</a></li>
      <li><a href="vendorbookings.php">Bookings</a></li>
      <li><a href="vendorearnings.php">Earnings</a></li>
      <li><a href="vendorreviews.php">Reviews</a></li>
      <li><a href="vendorprofile.php" class="active">Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="profile-container" role="main" aria-label="Vendor Profile Page">
    <div class="profile-header">
      <img src="https://randomuser.me/api/portraits/men/72.jpg" alt="Vendor Profile Picture" class="profile-avatar" />
      <div class="profile-name">Ravi Kumar</div>
      <div class="profile-title">Tailoring Specialist</div>
    </div>

    <form id="profileForm" aria-describedby="profileInstructions">
      <p id="profileInstructions" style="text-align:center; color:#4a5368; margin-bottom: 24px;">
        Update your profile details below and click Save.
      </p>
      <label for="vendorName">Full Name</label>
      <input type="text" id="vendorName" name="vendorName" value="Ravi Kumar" required />

      <label for="vendorEmail">Email Address</label>
      <input type="email" id="vendorEmail" name="vendorEmail" value="ravi.kumar@example.com" required />

      <label for="vendorPhone">Phone Number</label>
      <input type="tel" id="vendorPhone" name="vendorPhone" value="+91 9876543210" required />

      <label for="vendorAddress">Address</label>
      <textarea id="vendorAddress" name="vendorAddress" required>12, Sunrise Lane, HIG 17, Hyderabad</textarea>

      <label for="vendorBio">About You</label>
      <textarea id="vendorBio" name="vendorBio" placeholder="Brief description about your tailoring expertise and experience.">Experienced tailor specializing in men's and women's garments with over 10 years of custom work and alterations.</textarea>

      <button type="submit" aria-label="Save Profile">Save</button>
    </form>
  </div>

  <script>
    document.getElementById('profileForm').addEventListener('submit', function(event) {
      event.preventDefault();
      alert('Profile saved successfully!');
    });
  </script>
</body>
</html>