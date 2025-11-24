<?php 
require_once 'config.php';

$provider_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($provider_id == 0) {
    header("Location: services.php");
    exit();
}

$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        JOIN service_categories sc ON sp.category_id = sc.category_id 
        WHERE sp.provider_id = $provider_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: services.php");
    exit();
}

$provider = $result->fetch_assoc();

$reviews_sql = "SELECT r.*, u.full_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.provider_id = $provider_id 
                ORDER BY r.created_at DESC 
                LIMIT 10";
$reviews_result = $conn->query($reviews_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($provider['provider_name']); ?> - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --light-blue: #dbeafe;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dark);
            font-weight: 500;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--secondary-blue);
        }

        .btn-outline {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .btn-full {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .section {
            padding: 3rem 0;
        }

        .provider-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .provider-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            color: var(--text-light);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .provider-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .skill-tag {
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .booking-summary {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border);
        }

        .footer {
            background: #1e3a8a;
            color: var(--white);
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .footer-bottom {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 968px) {
            .detail-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-hands-helping"></i> Intzi
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if(isLoggedIn()): ?>
                    <li><a href="my-bookings.php">My Bookings</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="section">
        <div class="container">
            <a href="services.php" style="color: var(--primary-blue); margin-bottom: 1rem; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back to Services
            </a>
            
            <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem; margin-top: 2rem;">
                <div>
                    <div style="background: white; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;">
                        <div style="display: flex; gap: 2rem; align-items: start; flex-wrap: wrap;">
                            <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($provider['provider_name']); ?>"
                                 style="width: 200px; height: 200px; border-radius: 12px; object-fit: cover;"
                                 onerror="this.src='images/default-provider.jpg'">
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                                    <div>
                                        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($provider['provider_name']); ?></h1>
                                        <p style="color: var(--primary-blue); font-weight: 600; font-size: 1.1rem;"><?php echo htmlspecialchars($provider['category_name']); ?></p>
                                    </div>
                                    <div class="provider-rating">
                                        <i class="fas fa-star"></i> <?php echo $provider['rating']; ?>
                                    </div>
                                </div>
                                
                                <div class="provider-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-briefcase"></i> <?php echo $provider['experience_years']; ?> years
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-check-circle" style="color: var(--success);"></i> <?php echo $provider['total_bookings']; ?> bookings
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($provider['city']); ?>
                                    </span>
                                </div>

                                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 1.5rem;">
                                    <?php echo htmlspecialchars($provider['bio']); ?>
                                </p>

                                <?php if($provider['availability_status'] == 'available'): ?>
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; display: inline-block;">
                                        <i class="fas fa-check-circle"></i> Available Now
                                    </span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; display: inline-block;">
                                        <i class="fas fa-times-circle"></i> Currently Unavailable
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div style="background: white; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;">
                        <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-tools"></i> Skills & Expertise</h2>
                        <div class="provider-skills">
                            <?php 
                            $skills = explode(',', $provider['skills']);
                            foreach($skills as $skill): 
                            ?>
                            <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="background: white; padding: 2rem; border-radius: 16px;">
                        <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-star"></i> Customer Reviews</h2>
                        <?php if($reviews_result->num_rows > 0): ?>
                            <?php while($review = $reviews_result->fetch_assoc()): ?>
                            <div style="border-bottom: 1px solid var(--border); padding: 1.5rem 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                    <div style="color: var(--warning);">
                                        <?php for($i = 0; $i < $review['rating']; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="color: var(--text-light);"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                <small style="color: var(--text-light);"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: var(--text-light); text-align: center; padding: 2rem 0;">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="booking-summary">
                        <h3 style="margin-bottom: 1.5rem;">Book This Service</h3>
                        <div class="summary-item">
                            <span>Hourly Rate</span>
                            <strong>₹<?php echo number_format($provider['hourly_rate'], 2); ?></strong>
                        </div>
                        <div class="summary-item" style="border: none;">
                            <span>Minimum Duration</span>
                            <strong>1 hour</strong>
                        </div>
                        
                        <?php if(isLoggedIn()): ?>
                            <button onclick="window.location.href='booking.php?provider=<?php echo $provider_id; ?>'" 
                                    class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </button>
                        <?php else: ?>
                            <button onclick="window.location.href='login.php?redirect=<?php echo urlencode('booking.php?provider='.$provider_id); ?>'" 
                                    class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </button>
                        <?php endif; ?>
                        
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem; color: var(--text-light);">
                                <i class="fas fa-shield-alt" style="color: var(--primary-blue);"></i>
                                <span>Verified Professional</span>
                            </div>
                            <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem; color: var(--text-light);">
                                <i class="fas fa-lock" style="color: var(--primary-blue);"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div style="display: flex; gap: 0.8rem; color: var(--text-light);">
                                <i class="fas fa-headset" style="color: var(--primary-blue);"></i>
                                <span>24/7 Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Intzi. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
