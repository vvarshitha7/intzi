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
  <title>Provider Dashboard - INTZI</title>
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

    /* Analytics section with 4 cards in a row */
    .analytics-section {
      max-width: 1100px;
      margin: 0 auto 60px auto;
      padding: 0 24px;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 40px;
      margin-bottom: 0;
    }
    .stat-card {
      background: #fff;
      border-radius: 16px;
      padding: 28px 30px 32px;
      box-shadow: 0 6px 24px rgba(95, 100, 236, 0.15);
      border-left: 6px solid #5f64ec;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: default;
      user-select: none;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .stat-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 14px 35px rgba(95, 100, 236, 0.3);
    }
    .stat-card h3 {
      color: #5f64ec;
      font-size: 1rem;
      margin: 0 0 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }
    .stat-card .number {
      font-size: 2.6rem;
      font-weight: 800;
      color: #222;
      margin: 0;
      line-height: 1.1;
    }
    .stat-card .change {
      font-size: 0.9rem;
      margin-top: 6px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    .stat-card .change.positive {
      color: #27ae60;
    }
    .stat-card .change.negative {
      color: #e74c3c;
    }

    /* Two skill cards side by side */
    .skills-wrap {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 50px;
      max-width: 900px;
      margin: 0 auto 60px auto;
      padding: 0 16px;
    }
    .growth-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 24px rgba(112, 118, 236, 0.10);
      padding: 40px 36px 36px 36px;
      width: 100%;
      max-width: 440px;
      min-width: 320px;
      text-align: left;
      transition: box-shadow 0.2s;
    }
    .growth-card:hover {
      box-shadow: 0 12px 32px rgba(95, 100, 236, 0.16);
    }
    .growth-card h3 {
      color: #5f64ec;
      margin: 0 0 24px;
      font-size: 1.28rem;
      font-weight: 700;
      text-align: center;
      letter-spacing: 0.7px;
    }
    .skill-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      font-weight: 600;
      font-size: 1rem;
      color: #3a3f60;
    }
    .progress-bar {
      background: #e5e8ff;
      border-radius: 18px;
      height: 12px;
      margin: 8px 0 20px 0;
      overflow: hidden;
      box-shadow: inset 0 1px 2px rgba(255,255,255,0.8);
    }
    .progress-fill {
      background: linear-gradient(90deg, #5f64ec, #3f47d8 80%);
      height: 100%;
      border-radius: 18px;
      transition: width 0.45s;
      box-shadow: 0 2px 6px rgba(95, 100, 236, 0.23);
    }

    /* Responsive adjustments */
    @media (max-width: 940px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .skills-wrap {
        flex-direction: column;
        gap: 28px;
        align-items: center;
        max-width: 480px;
      }
      .growth-card {
        max-width: 420px;
        min-width: 0;
      }
    }
    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
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
    <h1>Tailoring Services</h1>
    <p>Expert in custom stitching, alterations, and garment repairs tailored to your needs.</p>
  </div>

  <div class="service-summary">
    <div class="icon" aria-label="Tailoring Icon" title="Tailoring Icon">✂️</div>
    <h3>Tailoring Services</h3>
    <p>Expert in custom stitching, alterations, and garment repairs tailored to your needs.</p>
  </div>

  <!-- Analytics Section -->
  <section class="analytics-section" aria-label="Key Performance Analytics">
    <div class="stats-grid">
      <article class="stat-card" aria-label="This Week's Bookings">
        <h3>This Week's Bookings</h3>
        <p class="number">18</p>
        <p class="change positive">+10% from last week</p>
      </article>

      <article class="stat-card" aria-label="This Month's Bookings">
        <h3>This Month's Bookings</h3>
        <p class="number">70</p>
        <p class="change positive">+22% from last month</p>
      </article>
      <article class="stat-card" aria-label="Total Earnings this month">
        <h3>Total Earnings (Month)</h3>
        <p class="number">₹15,200</p>
        <p class="change positive">+30% increase</p>
      </article>
      <article class="stat-card" aria-label="Customer Rating">
        <h3>Customer Rating</h3>
        <p class="number">4.7</p>
        <p class="change positive">+0.1 this month</p>
      </article>
    </div>
  </section>

  <!-- Top Performing Skills -->
  <div class="skills-wrap" aria-label="Top Performing Skills">
    <section class="growth-card" aria-label="Top Performing Skills for Women">
      <h3>Top Performing Skills (Women)</h3>
      <div class="skill-item">
        <span>Bridal Dress Alterations</span>
        <span>85%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:85%;"></div>
      </div>
      <div class="skill-item">
        <span>Women's Formal Wear</span>
        <span>80%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:80%;"></div>
      </div>
      <div class="skill-item">
        <span>Casual Dresses</span>
        <span>75%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:75%;"></div>
      </div>
    </section>

    <section class="growth-card" aria-label="Top Performing Skills for Men">
      <h3>Top Performing Skills (Men)</h3>
      <div class="skill-item">
        <span>Men's Suit Stitching</span>
        <span>92%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:92%;"></div>
      </div>
      <div class="skill-item">
        <span>Men's Casual Wear</span>
        <span>78%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:78%;"></div>
      </div>
      <div class="skill-item">
        <span>Men's Formal Shirts</span>
        <span>72%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:72%;"></div>
      </div>
    </section>
  </div>
</body>
</html>
