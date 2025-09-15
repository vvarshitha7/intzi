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
  <title>Earnings - INTZI Vendor</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', Arial, sans-serif;
      background: #f7f8fc;
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
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .navbar .brand {
      color: #fff;
      font-weight: 700;
      font-size: 1.8rem;
      letter-spacing: 2.5px;
      flex-shrink: 0;
      text-transform: uppercase;
    }
    .navbar ul {
      list-style: none;
      display: flex;
      flex-grow: 1;
      justify-content: space-between;
      margin: 0 0 0 48px;
      padding: 0;
    }
    .navbar ul li {
      flex: 1;
      text-align: center;
    }
    .navbar ul li a {
      color: #c3c9f9;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      padding: 18px 0;
      display: block;
      border-radius: 6px 6px 0 0;
      transition: background-color 0.3s, color 0.3s;
    }
    .navbar ul li a:hover {
      background-color: #3e47d3;
      color: #eef2ff;
    }
    .navbar ul li a.active {
      background-color: #eef2ff;
      color: #5f64ec;
      font-weight: 700;
    }
    .hero {
      background: linear-gradient(135deg, #5f64ec 0%, #4353bb 100%);
      color: #fff;
      padding: 48px 20px 30px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(95, 100, 236, 0.3);
      border-bottom-left-radius: 24px;
      border-bottom-right-radius: 24px;
      margin-bottom: 36px;
    }
    .hero h1 {
      font-size: 2.5rem;
      margin: 0 0 12px;
      letter-spacing: 2px;
      font-weight: 700;
      text-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .hero p {
      font-size: 1.2rem;
      margin: 0;
      font-weight: 500;
      color: #d0d6ff;
    }
    .earnings-summary {
      max-width: 900px;
      margin: 0 auto 48px auto;
      padding: 20px 30px;
      background: #eef2ff;
      border-radius: 18px;
      box-shadow: 0 6px 16px rgba(95, 100, 236, 0.1);
      color: #2d317e;
      font-weight: 600;
      font-size: 1rem;
      display: flex;
      justify-content: space-around;
      text-align: center;
    }
    .earnings-summary div {
      flex: 1;
    }
    .earnings-summary h3 {
      margin: 0 0 8px;
      font-weight: 700;
      color: #5f64ec;
    }
    .earnings-summary p {
      margin: 0;
      font-size: 1.6rem;
      font-weight: 800;
      color: #222;
    }

    /* Earnings Table */
    .earnings-table-section {
      max-width: 900px;
      margin: 0 auto 60px auto;
      padding: 0 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0 6px 24px rgba(95, 100, 236, 0.15);
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
    }
    thead {
      background: #5f64ec;
      color: #fff;
    }
    th, td {
      padding: 16px 24px;
      text-align: left;
    }
    tbody tr:hover {
      background: #f1f3ff;
    }
    th {
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.9rem;
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

  <div class="hero">
    <h1>Earnings Overview</h1>
    <p>Track your total earnings and review detailed income records from your tailoring services.</p>
  </div>

  <section class="earnings-summary" aria-label="Earnings Summary Highlights">
    <div>
      <h3>This Week</h3>
      <p>₹4,500</p>
    </div>
    <div>
      <h3>This Month</h3>
      <p>₹15,200</p>
    </div>
    <div>
      <h3>Year to Date</h3>
      <p>₹136,800</p>
    </div>
  </section>

  <section class="earnings-table-section" aria-label="Detailed Earnings Records">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Service</th>
          <th>Booking ID</th>
          <th>Amount Earned</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>2025-09-12</td>
          <td>Men's Suit Stitching</td>
          <td>#BS1001</td>
          <td>₹2,300</td>
        </tr>
        <tr>
          <td>2025-09-11</td>
          <td>Bridal Dress Alterations</td>
          <td>#BD1043</td>
          <td>₹3,200</td>
        </tr>
        <tr>
          <td>2025-09-10</td>
          <td>Women's Formal Wear</td>
          <td>#WF1027</td>
          <td>₹1,100</td>
        </tr>
        <tr>
          <td>2025-09-09</td>
          <td>Casual Dresses</td>
          <td>#CD1005</td>
          <td>₹900</td>
        </tr>
        <tr>
          <td>2025-09-08</td>
          <td>Men's Casual Wear</td>
          <td>#MC1017</td>
          <td>₹1,000</td>
        </tr>
      </tbody>
    </table>
  </section>
</body>
</html>