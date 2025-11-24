<?php 
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, sp.provider_name, sp.profile_image, sc.category_name 
        FROM bookings b 
        JOIN service_providers sp ON b.provider_id = sp.provider_id 
        JOIN service_categories sc ON b.category_id = sc.category_id 
        WHERE b.user_id = $user_id 
        ORDER BY b.created_at DESC";
$bookings_result = $conn->query($sql);

$success_message = isset($_SESSION['booking_success']) ? $_SESSION['booking_success'] : '';
unset($_SESSION['booking_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Intzi</title>
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
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
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

        .btn-outline {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .section {
            padding: 3rem 0;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .booking-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
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

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }
            .booking-grid {
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
                <li><a href="my-bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="section">
        <div class="container">
            <h1 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem;">My Bookings</h1>
            
            <?php if($success_message): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($bookings_result->num_rows > 0): ?>
                <?php while($booking = $bookings_result->fetch_assoc()): ?>
                <div class="booking-card">
                    <div class="booking-grid" style="display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: center;">
                        <img src="images/providers/<?php echo htmlspecialchars($booking['profile_image']); ?>" 
                             style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover;"
                             onerror="this.src='images/default-provider.jpg'">
                        
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <div>
                                    <h3 style="font-size: 1.3rem; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($booking['provider_name']); ?></h3>
                                    <p style="color: var(--text-light); font-size: 0.9rem;"><?php echo htmlspecialchars($booking['category_name']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                    <?php echo ucfirst($booking['booking_status']); ?>
                                </span>
                            </div>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                                <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking['booking_time'])); ?></span>
                                <span><i class="fas fa-hourglass-half"></i> <?php echo $booking['duration_hours']; ?> hours</span>
                                <span><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($booking['total_amount'], 2); ?></span>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <a href="service-details.php?id=<?php echo $booking['provider_id']; ?>" 
                               style="color: var(--primary-blue); font-weight: 600; display: block; margin-bottom: 0.5rem;">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                            <?php if($booking['booking_status'] == 'completed' && !$booking['reviewed']): ?>
                                <a href="#" style="color: var(--warning); font-weight: 600;">
                                    <i class="fas fa-star"></i> Write Review
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                    <h3>No bookings yet</h3>
                    <p style="color: var(--text-light); margin: 1rem 0;">Start booking services from trusted local providers</p>
                    <a href="services.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; padding: 0.8rem 2rem;">
                        Browse Services
                    </a>
                </div>
            <?php endif; ?>
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
