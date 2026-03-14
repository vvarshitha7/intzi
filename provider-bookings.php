<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$sql = "SELECT b.*, u.full_name, u.phone, u.email 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        WHERE b.provider_id = $provider_id";

if($filter != 'all') {
    $sql .= " AND b.booking_status = '$filter'";
}

$sql .= " ORDER BY b.created_at DESC";
$bookings_result = $conn->query($sql);

$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Provider Dashboard</title>
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
            --danger: #ef4444;
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

        .sidebar-menu { list-style: none; }

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
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .filter-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            background: white;
            border: 2px solid var(--border);
            cursor: pointer;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: all 0.3s;
        }

        .filter-tab.active {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            margin-bottom: 1rem;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending   { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .btn {
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success { background: var(--success); color: white; }
        .btn-danger  { background: var(--danger);  color: white; }
        .btn-primary { background: var(--primary-blue); color: white; }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error   { background: #fee2e2; color: #991b1b; }

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
                <li><a href="provider-bookings.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">My Bookings</h1>

            <?php if($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="filter-tabs">
                <a href="?filter=all"       class="filter-tab <?php echo $filter == 'all'       ? 'active' : ''; ?>">All</a>
                <a href="?filter=pending"   class="filter-tab <?php echo $filter == 'pending'   ? 'active' : ''; ?>">Pending</a>
                <a href="?filter=confirmed" class="filter-tab <?php echo $filter == 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="?filter=completed" class="filter-tab <?php echo $filter == 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="?filter=cancelled" class="filter-tab <?php echo $filter == 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
            </div>

            <div class="card">
                <?php if($bookings_result->num_rows > 0): ?>
                    <?php while($booking = $bookings_result->fetch_assoc()):

                        // FIX 1: Calculate duration from start_time and end_time
                        $duration_text = 'N/A';
                        if(!empty($booking['start_time']) && !empty($booking['end_time'])) {
                            $start = new DateTime($booking['start_time']);
                            $end   = new DateTime($booking['end_time']);
                            $diff  = $start->diff($end);
                            $duration_text = '';
                            if($diff->h > 0) $duration_text .= $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                            if($diff->i > 0) $duration_text .= ($diff->h > 0 ? ' ' : '') . $diff->i . ' min';
                            if(empty($duration_text)) $duration_text = 'N/A';
                        }

                        // FIX 2: Build address from individual columns
                        $address_parts = array_filter([
                            $booking['house_number'] ?? '',
                            $booking['street']       ?? '',
                            $booking['area']         ?? '',
                            !empty($booking['pincode']) ? 'Hyderabad - ' . $booking['pincode'] : ''
                        ]);
                        $full_address = !empty($address_parts) ? implode(', ', $address_parts) : 'No address provided';
                    ?>
                    <div class="booking-item">
                        <div class="booking-header">
                            <div>
                                <strong style="font-size: 1.1rem; color: var(--primary-blue);">
                                    #<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?>
                                </strong>
                                <div><?php echo htmlspecialchars($booking['full_name']); ?></div>
                                <div style="color: var(--text-light); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($booking['phone']); ?> | <?php echo htmlspecialchars($booking['email']); ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                            <div><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
                            <div><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($booking['booking_time'])); ?></div>
                            <div><i class="fas fa-hourglass-half"></i> <?php echo $duration_text; ?></div>
                            <div><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($booking['total_amount'], 2); ?></div>
                        </div>

                        <div style="margin: 1rem 0;">
                            <strong>Address:</strong> <?php echo htmlspecialchars($full_address); ?>
                        </div>

                        <?php if(!empty($booking['special_requests'])): ?>
                        <div style="margin: 0.5rem 0; color: var(--text-light); font-size: 0.9rem;">
                            <strong>Special Requests:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if($booking['booking_status'] == 'pending'): ?>
                        <div style="display: flex; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                            <form method="POST" action="update-booking-status.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Accept Booking
                                </button>
                            </form>
                            <form method="POST" action="update-booking-status.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this booking?')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>

                        <?php if($booking['booking_status'] == 'confirmed'): ?>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                            <form method="POST" action="update-booking-status.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Mark as Completed
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--text-light);"></i>
                        <p style="margin-top: 1rem;">No bookings found</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
