<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];
$provider = getProviderDetails($provider_id);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN booking_status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
    SUM(CASE WHEN booking_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN booking_status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
    SUM(CASE WHEN payment_status = 'completed' THEN total_amount ELSE 0 END) as total_earnings
    FROM bookings WHERE provider_id = $provider_id";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get recent bookings
$bookings_sql = "SELECT b.*, u.full_name, u.phone, u.email 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.user_id 
                 WHERE b.provider_id = $provider_id 
                 ORDER BY b.created_at DESC 
                 LIMIT 10";
$bookings_result = $conn->query($bookings_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - Intzi</title>
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
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .top-bar h1 {
            font-size: 2rem;
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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.blue {
            background: var(--primary-blue);
            color: white;
        }

        .stat-icon.green {
            background: var(--success);
            color: white;
        }

        .stat-icon.orange {
            background: var(--warning);
            color: white;
        }

        .stat-icon.red {
            background: var(--danger);
            color: white;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 0.2rem;
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-header h2 {
            font-size: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .booking-item {
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .booking-id {
            font-weight: 700;
            color: var(--primary-blue);
            font-size: 1.1rem;
        }

        .status-badge {
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

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .booking-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 968px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-hands-helping"></i> Intzi</h2>
                <p>Provider Portal</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="provider-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="provider-bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-earnings.php"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1>Welcome, <?php echo htmlspecialchars($provider['provider_name']); ?>! 👋</h1>
                    <p style="color: var(--text-light);">Here's what's happening with your services today</p>
                </div>
                <a href="provider-profile.php" class="btn btn-primary">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_bookings']; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_bookings']; ?></h3>
                        <p>Pending Requests</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['completed_bookings']; ?></h3>
                        <p>Completed</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₹<?php echo number_format($stats['total_earnings'], 0); ?></h3>
                        <p>Total Earnings</p>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header">
                    <h2>Recent Booking Requests</h2>
                    <a href="provider-bookings.php" class="btn btn-primary btn-sm">View All</a>
                </div>

                <?php if($bookings_result->num_rows > 0): ?>
                    <?php while($booking = $bookings_result->fetch_assoc()): ?>
                    <div class="booking-item">
                        <div class="booking-header">
                            <div>
                                <div class="booking-id">#<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></div>
                                <strong><?php echo htmlspecialchars($booking['full_name']); ?></strong>
                            </div>
                            <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                        </div>

                        <div class="booking-details">
                            <div>
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                            </div>
                            <div>
                                <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking['booking_time'])); ?>
                            </div>
                            <div>
                                <i class="fas fa-hourglass-half"></i> <?php echo $booking['duration_hours']; ?> hours
                            </div>
                            <div>
                                <i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($booking['total_amount'], 2); ?>
                            </div>
                        </div>

                        <div>
                            <strong>Address:</strong> <?php echo htmlspecialchars($booking['address']); ?>
                        </div>

                        <?php if($booking['special_requirements']): ?>
                        <div style="margin-top: 0.5rem;">
                            <strong>Special Requirements:</strong> <?php echo htmlspecialchars($booking['special_requirements']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if($booking['booking_status'] == 'pending'): ?>
                        <div class="booking-actions">
                            <form method="POST" action="update-booking-status.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                            </form>
                            <form method="POST" action="update-booking-status.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this booking?')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                            <a href="tel:<?php echo $booking['phone']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-phone"></i> Call Customer
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>No bookings yet. Your services will appear to customers once approved by admin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
