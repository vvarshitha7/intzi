<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];

// Get review statistics
$stats_sql = "SELECT 
    COUNT(*) as total_reviews,
    AVG(rating) as avg_rating,
    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
    FROM reviews 
    WHERE provider_id = $provider_id";
$stats = $conn->query($stats_sql)->fetch_assoc();

// Get all reviews
$reviews_sql = "SELECT r.*, u.full_name, b.booking_date 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                JOIN bookings b ON r.booking_id = b.booking_id 
                WHERE r.provider_id = $provider_id 
                ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews - Provider Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --warning: #f59e0b;
            --border: #e5e7eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--warning);
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .rating-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .bar {
            flex: 1;
            height: 10px;
            background: var(--bg-light);
            border-radius: 5px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: var(--warning);
        }

        .review-item {
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .stars {
            color: var(--warning);
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-hands-helping"></i> Intzi</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="provider-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="provider-bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-earnings.php"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Customer Reviews</h1>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?> <i class="fas fa-star" style="font-size: 1.5rem;"></i></h3>
                    <p style="color: var(--text-light);">Average Rating</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['total_reviews'] ?? 0; ?></h3>
                    <p style="color: var(--text-light);">Total Reviews</p>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Rating Distribution</h2>
                <?php 
                $total = $stats['total_reviews'] ?? 1;
                ?>
                <div class="rating-bar">
                    <span>5 <i class="fas fa-star stars"></i></span>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?php echo ($stats['five_star']/$total*100); ?>%;"></div>
                    </div>
                    <span><?php echo $stats['five_star']; ?></span>
                </div>
                <div class="rating-bar">
                    <span>4 <i class="fas fa-star stars"></i></span>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?php echo ($stats['four_star']/$total*100); ?>%;"></div>
                    </div>
                    <span><?php echo $stats['four_star']; ?></span>
                </div>
                <div class="rating-bar">
                    <span>3 <i class="fas fa-star stars"></i></span>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?php echo ($stats['three_star']/$total*100); ?>%;"></div>
                    </div>
                    <span><?php echo $stats['three_star']; ?></span>
                </div>
                <div class="rating-bar">
                    <span>2 <i class="fas fa-star stars"></i></span>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?php echo ($stats['two_star']/$total*100); ?>%;"></div>
                    </div>
                    <span><?php echo $stats['two_star']; ?></span>
                </div>
                <div class="rating-bar">
                    <span>1 <i class="fas fa-star stars"></i></span>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?php echo ($stats['one_star']/$total*100); ?>%;"></div>
                    </div>
                    <span><?php echo $stats['one_star']; ?></span>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">All Reviews</h2>
                <?php if($reviews_result->num_rows > 0): ?>
                    <?php while($review = $reviews_result->fetch_assoc()): ?>
                    <div class="review-item">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                            <div class="stars">
                                <?php for($i = 0; $i < $review['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                        <small style="color: var(--text-light);">
                            Service on <?php echo date('M d, Y', strtotime($review['booking_date'])); ?> • 
                            Reviewed on <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                        </small>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-star" style="font-size: 3rem;"></i>
                        <p style="margin-top: 1rem;">No reviews yet. Complete bookings to receive reviews!</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
