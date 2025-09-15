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
  <title>Reviews - INTZI Vendor</title>
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
    .reviews-section {
      max-width: 900px;
      margin: 0 auto 48px auto;
      padding: 0 20px;
    }
    .review-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 24px rgba(95, 100, 236, 0.15);
      padding: 24px 28px;
      margin-bottom: 22px;
    }
    .review-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }
    .review-user {
      font-weight: 700;
      font-size: 1rem;
      color: #5f64ec;
    }
    .review-date {
      font-size: 0.85rem;
      color: #999;
    }
    .review-rating {
      color: #27ae60;
      font-weight: 700;
      font-size: 1.1rem;
    }
    .review-text {
      font-size: 1rem;
      color: #333;
      line-height: 1.5;
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
    <h1>Customer Reviews</h1>
    <p>Read what your customers have to say about your tailoring services.</p>
  </div>

  <section class="reviews-section" aria-label="Customer reviews list">
    <article class="review-card">
      <div class="review-header">
        <span class="review-user">Anita Sharma</span>
        <span class="review-date">2025-09-10</span>
        <span class="review-rating">★★★★☆</span>
      </div>
      <p class="review-text">Excellent craftsmanship on my bridal dress alterations. Very professional and attentive to detail.</p>
    </article>

    <article class="review-card">
      <div class="review-header">
        <span class="review-user">Ravi Kumar</span>
        <span class="review-date">2025-09-08</span>
        <span class="review-rating">★★★★★</span>
      </div>
      <p class="review-text">Very satisfied with the suit stitching. Perfect fit and great fabric choice recommendations.</p>
    </article>

    <article class="review-card">
      <div class="review-header">
        <span class="review-user">Meena Joshi</span>
        <span class="review-date">2025-09-05</span>
        <span class="review-rating">★★★★☆</span>
      </div>
      <p class="review-text">Good service and quick turnaround on my formal wear repair. Will order again!</p>
    </article>
  </section>
</body>
</html>