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
  <title>My Services - INTZI Vendor</title>
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
    .service-summary {
      text-align: center;
      margin-bottom: 36px;
      padding: 0 10px;
    }
    .service-summary .icon {
      font-size: 3.2rem;
      color: #5f64ec;
      margin-bottom: 12px;
      display: inline-block;
    }
    .service-summary h3 {
      margin: 0 0 10px;
      font-size: 2rem;
      font-weight: 700;
      color: #232a4f;
      letter-spacing: 1px;
    }
    .service-summary p {
      font-size: 1.07rem;
      color: #4a5368;
      margin: 0 auto;
      max-width: 540px;
      line-height: 1.6;
    }


    /* Service Bookings Table */
    .bookings-section {
      max-width: 900px;
      margin: 0 auto 48px auto;
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


    /* Feature Highlights */
    .feature-summary {
      max-width: 900px;
      margin: 0 auto 36px auto;
      padding: 20px 30px;
      background: #eef2ff;
      border-radius: 18px;
      box-shadow: 0 6px 16px rgba(95, 100, 236, 0.1);
      color: #2d317e;
      font-weight: 600;
      font-size: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .feature-summary span {
      display: flex;
      align-items: center;
    }
    .feature-summary .highlight {
      font-weight: 700;
      font-size: 1.2rem;
      margin-left: 8px;
      color: #5f64ec;
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
    <h1>My Tailoring Services</h1>
    <p>Review your bookings by service category and discover your most popular offerings.</p>
  </div>

  <div class="service-summary">
    <div class="icon" aria-label="Tailoring Icon" title="Tailoring Icon">✂️</div>
    <h3>Tailoring Services Overview</h3>
    <p>See how many times each type of service has been booked and get insights to boost your business.</p>
  </div>

  <section class="bookings-section" aria-label="Bookings by Service Type">
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <th>Bookings</th>
          <th>Percentage of Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Bridal Dress Alterations</td>
          <td>35</td>
          <td>29%</td>
        </tr>
        <tr>
          <td>Men's Suit Stitching</td>
          <td>42</td>
          <td>35%</td>
        </tr>
        <tr>
          <td>Women's Formal Wear</td>
          <td>22</td>
          <td>18%</td>
        </tr>
        <tr>
          <td>Casual Dresses</td>
          <td>15</td>
          <td>12%</td>
        </tr>
        <tr>
          <td>Men's Casual Wear</td>
          <td>10</td>
          <td>8%</td>
        </tr>
      </tbody>
    </table>
  </section>

  <div class="feature-summary">
    <span>Most Booked Service: <span class="highlight">Men's Suit Stitching (35%)</span></span>
    <span>Total Bookings: <span class="highlight">124</span></span>
  </div>
</body>
</html>