<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];

// Get earnings statistics
$stats_sql = "SELECT 
    COUNT(*) as total_completed,
    SUM(total_amount) as total_earnings,
    AVG(total_amount) as avg_booking_value,
    SUM(CASE WHEN MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE()) THEN total_amount ELSE 0 END) as this_month_earnings
    FROM bookings 
    WHERE provider_id = $provider_id AND payment_status = 'completed' AND booking_status = 'completed'";
$stats = $conn->query($stats_sql)->fetch_assoc();

// Get earnings history
$earnings_sql = "SELECT b.*, u.full_name, sc.category_name 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.user_id 
                 JOIN service_categories sc ON b.category_id = sc.category_id 
                 WHERE b.provider_id = $provider_id 
                 AND b.payment_status = 'completed' 
                 ORDER BY b.payment_date DESC";
$earnings_result = $conn->query($earnings_sql);

// Monthly earnings for chart
$monthly_sql = "SELECT 
    DATE_FORMAT(payment_date, '%b %Y') as month,
    SUM(total_amount) as earnings
    FROM bookings 
    WHERE provider_id = $provider_id 
    AND payment_status = 'completed'
    AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY payment_date DESC";
$monthly_result = $conn->query($monthly_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Earnings - Provider Dashboard</title>
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
            color: var(--success);
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        table th {
            background: var(--bg-light);
            font-weight: 600;
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
                <li><a href="provider-earnings.php" class="active"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">My Earnings</h1>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>₹<?php echo number_format($stats['total_earnings'] ?? 0, 2); ?></h3>
                    <p style="color: var(--text-light);">Total Earnings</p>
                </div>

                <div class="stat-card">
                    <h3>₹<?php echo number_format($stats['this_month_earnings'] ?? 0, 2); ?></h3>
                    <p style="color: var(--text-light);">This Month</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['total_completed'] ?? 0; ?></h3>
                    <p style="color: var(--text-light);">Completed Jobs</p>
                </div>

                <div class="stat-card">
                    <h3>₹<?php echo number_format($stats['avg_booking_value'] ?? 0, 2); ?></h3>
                    <p style="color: var(--text-light);">Average Booking</p>
                </div>
            </div>

            <!-- Monthly Breakdown -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Monthly Earnings (Last 6 Months)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($month = $monthly_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $month['month']; ?></td>
                            <td style="color: var(--success); font-weight: 600;">₹<?php echo number_format($month['earnings'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Earnings History -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Transaction History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($earnings_result->num_rows > 0): ?>
                            <?php while($earning = $earnings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($earning['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($earning['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($earning['category_name']); ?></td>
                                <td><?php echo $earning['duration_hours']; ?> hrs</td>
                                <td style="color: var(--success); font-weight: 600;">₹<?php echo number_format($earning['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($earning['payment_method'] ?? 'N/A'); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                    No earnings yet. Complete bookings to start earning!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
