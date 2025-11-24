<?php 
require_once 'config.php';
requireAdminLogin();

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'total_providers' => $conn->query("SELECT COUNT(*) as count FROM service_providers")->fetch_assoc()['count'],
    'pending_providers' => $conn->query("SELECT COUNT(*) as count FROM service_providers WHERE account_status = 'pending'")->fetch_assoc()['count'],
    'total_bookings' => $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'],
    'pending_bookings' => $conn->query("SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'")->fetch_assoc()['count'],
    'total_revenue' => $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'completed'")->fetch_assoc()['total'] ?? 0
];

// Recent activities
$recent_bookings = $conn->query("SELECT b.*, u.full_name as user_name, sp.provider_name 
                                 FROM bookings b 
                                 JOIN users u ON b.user_id = u.user_id 
                                 JOIN service_providers sp ON b.provider_id = sp.provider_id 
                                 ORDER BY b.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Intzi</title>
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
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
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
            color: var(--primary-blue);
        }

        .stat-card p {
            color: var(--text-light);
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card h2 {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
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

        .status-badge {
            padding: 0.3rem 0.8rem;
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
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
                <p>Intzi Management</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="admin-providers.php"><i class="fas fa-user-tie"></i> Providers</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-bookings.php"><i class="fas fa-calendar"></i> Bookings</a></li>
                <li><a href="admin-categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1>Dashboard Overview</h1>
                    <p style="color: var(--text-light);">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p><i class="fas fa-users"></i> Total Users</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['total_providers']; ?></h3>
                    <p><i class="fas fa-user-tie"></i> Total Providers</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['pending_providers']; ?></h3>
                    <p><i class="fas fa-clock"></i> Pending Approvals</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['total_bookings']; ?></h3>
                    <p><i class="fas fa-calendar-check"></i> Total Bookings</p>
                </div>

                <div class="stat-card">
                    <h3><?php echo $stats['pending_bookings']; ?></h3>
                    <p><i class="fas fa-hourglass-half"></i> Pending Bookings</p>
                </div>

                <div class="stat-card">
                    <h3>₹<?php echo number_format($stats['total_revenue'], 0); ?></h3>
                    <p><i class="fas fa-rupee-sign"></i> Total Revenue</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="admin-providers.php?filter=pending" class="btn btn-primary">
                        <i class="fas fa-user-check"></i> Approve Providers (<?php echo $stats['pending_providers']; ?>)
                    </a>
                    <a href="admin-bookings.php" class="btn btn-primary">
                        <i class="fas fa-calendar"></i> View All Bookings
                    </a>
                    <a href="admin-users.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <h2>
                    Recent Bookings
                    <a href="admin-bookings.php" class="btn btn-primary btn-sm">View All</a>
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Provider</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['provider_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo $booking['booking_status']; ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
