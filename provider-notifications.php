<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];

// Mark notification as read
if(isset($_GET['mark_read'])) {
    $notification_id = (int)$_GET['mark_read'];
    $conn->query("UPDATE provider_notifications SET is_read = 1 WHERE notification_id = $notification_id AND provider_id = $provider_id");
    header("Location: provider-notifications.php");
    exit();
}

// Get all notifications
$notifications_sql = "SELECT * FROM provider_notifications 
                     WHERE provider_id = $provider_id 
                     ORDER BY created_at DESC";
$notifications_result = $conn->query($notifications_sql);

// Get unread count
$unread_sql = "SELECT COUNT(*) as unread FROM provider_notifications WHERE provider_id = $provider_id AND is_read = 0";
$unread_result = $conn->query($unread_sql);
$unread_count = $unread_result->fetch_assoc()['unread'];

// Check if account is suspended
$provider_sql = "SELECT account_status, suspension_reason, suspension_date FROM service_providers WHERE provider_id = $provider_id";
$provider_result = $conn->query($provider_sql);
$provider = $provider_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Intzi</title>
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
            position: relative;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        .notification-badge {
            position: absolute;
            right: 1rem;
            background: var(--danger);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .suspension-alert {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 2px solid var(--danger);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .suspension-alert h2 {
            color: #991b1b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suspension-alert p {
            color: #7f1d1d;
            margin-bottom: 0.5rem;
        }

        .notification-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .notification-item.unread {
            background: #eff6ff;
            border-left-color: var(--primary-blue);
        }

        .notification-item.suspension {
            background: #fee2e2;
            border-left-color: var(--danger);
        }

        .notification-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .notification-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-dark);
        }

        .notification-time {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        .notification-message {
            color: var(--text-dark);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .notification-action {
            background: #fef3c7;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--warning);
            margin-top: 1rem;
        }

        .notification-action strong {
            color: #92400e;
            display: block;
            margin-bottom: 0.5rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            display: inline-block;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .type-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .type-icon.suspension {
            background: #fee2e2;
            color: var(--danger);
        }

        .type-icon.approval {
            background: #d1fae5;
            color: var(--success);
        }

        .type-icon.booking {
            background: #dbeafe;
            color: var(--primary-blue);
        }

        .type-icon.general {
            background: #e5e7eb;
            color: var(--text-light);
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
                <li><a href="provider-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="provider-bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-catalog.php"><i class="fas fa-list"></i> My Catalog</a></li>
                <li><a href="provider-notifications.php" class="active">
                    <i class="fas fa-bell"></i> Notifications
                    <?php if($unread_count > 0): ?>
                        <span class="notification-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="provider-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-earnings.php"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-bell"></i> Notifications</h1>
                <p style="color: var(--text-light);">Stay updated with important alerts and messages</p>
            </div>

            <!-- Suspension Alert -->
            <?php if($provider['account_status'] == 'suspended'): ?>
            <div class="suspension-alert">
                <h2><i class="fas fa-exclamation-triangle"></i> Account Suspended</h2>
                <p><strong>Your provider account has been suspended.</strong></p>
                <?php 
                $suspension_date_display = 'Not specified';
                if(!empty($provider_data['suspension_date']) && $provider_data['suspension_date'] != '0000-00-00 00:00:00') {
                    $suspension_date_display = date('F d, Y', strtotime($provider_data['suspension_date']));
                }
                ?>
                <p><strong>Date:</strong> <?php echo $suspension_date_display; ?></p>
                <?php 
                $suspension_reason_display = 'No reason provided';
                if(!empty($provider_data['suspension_reason'])) {
                    $suspension_reason_display = htmlspecialchars($provider_data['suspension_reason']);
                }
                ?>
                <p><strong>Reason:</strong> <?php echo $suspension_reason_display; ?></p>
                
                <div style="margin-top: 1.5rem; padding: 1.5rem; background: white; border-radius: 8px;">
                    <h3 style="color: var(--text-dark); margin-bottom: 1rem;">What happens now?</h3>
                    <ul style="color: #7f1d1d; margin-left: 1.5rem; line-height: 1.8;">
                        <li>Your profile is no longer visible to customers</li>
                        <li>You cannot receive new bookings</li>
                        <li>Existing bookings may be cancelled</li>
                        <li>Check your notifications below for next steps</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Notifications List -->
            <?php if($notifications_result->num_rows > 0): ?>
                <?php while($notification = $notifications_result->fetch_assoc()): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?> <?php echo $notification['notification_type']; ?>">
                    <div style="display: flex; align-items: start;">
                        <div class="type-icon <?php echo $notification['notification_type']; ?>">
                            <?php 
                            $icons = [
                                'suspension' => 'fa-exclamation-triangle',
                                'approval' => 'fa-check-circle',
                                'booking' => 'fa-calendar-check',
                                'payment' => 'fa-rupee-sign',
                                'general' => 'fa-info-circle'
                            ];
                            echo '<i class="fas ' . $icons[$notification['notification_type']] . '"></i>';
                            ?>
                        </div>
                        <div style="flex: 1;">
                            <div class="notification-header">
                                <div>
                                    <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                    <?php if(!$notification['is_read']): ?>
                                        <span style="display: inline-block; background: var(--primary-blue); color: white; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.75rem; margin-top: 0.3rem;">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <div class="notification-time">
                                    <i class="fas fa-clock"></i> 
                                    <?php 
                                    $time_diff = time() - strtotime($notification['created_at']);
                                    if($time_diff < 3600) {
                                        echo floor($time_diff / 60) . ' mins ago';
                                    } elseif($time_diff < 86400) {
                                        echo floor($time_diff / 3600) . ' hours ago';
                                    } else {
                                        echo date('M d, Y', strtotime($notification['created_at']));
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="notification-message">
                                <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
                            </div>
                            
                            <?php if(!empty($notification['action_required'])): ?>
                            <div class="notification-action">
                                <strong><i class="fas fa-tasks"></i> Action Required:</strong>
                                <p style="color: #78350f; margin: 0;"><?php echo nl2br(htmlspecialchars($notification['action_required'])); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if(!$notification['is_read']): ?>
                            <div style="margin-top: 1rem;">
                                <a href="?mark_read=<?php echo $notification['notification_id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i> Mark as Read
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications yet</h3>
                    <p>You'll see important updates and alerts here</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
