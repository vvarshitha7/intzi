<?php 
require_once 'config.php';
requireAdminLogin();

$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';
$success = isset($_SESSION['admin_success']) ? $_SESSION['admin_success'] : '';
unset($_SESSION['admin_success']);

// Build query
$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        LEFT JOIN service_categories sc ON sp.category_id = sc.category_id";

if($filter != 'all') {
    $sql .= " WHERE sp.account_status = '$filter'";
}

$sql .= " ORDER BY sp.created_at DESC";

$providers_result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Providers - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
            --danger: #ef4444;
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

        .provider-item {
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .provider-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
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

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-suspended {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="admin-providers.php" class="active"><i class="fas fa-user-tie"></i> Providers</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-bookings.php"><i class="fas fa-calendar"></i> Bookings</a></li>
                <li><a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Service Providers Management</h1>

            <?php if($success): ?>
                <div class="alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="filter-tabs">
                <a href="?filter=all" class="filter-tab <?php echo $filter == 'all' ? 'active' : ''; ?>">All Providers</a>
                <a href="?filter=pending" class="filter-tab <?php echo $filter == 'pending' ? 'active' : ''; ?>">Pending Approval</a>
                <a href="?filter=active" class="filter-tab <?php echo $filter == 'active' ? 'active' : ''; ?>">Active</a>
                <a href="?filter=suspended" class="filter-tab <?php echo $filter == 'suspended' ? 'active' : ''; ?>">Suspended</a>
            </div>

            <div class="card">
                <?php if($providers_result->num_rows > 0): ?>
                    <?php while($provider = $providers_result->fetch_assoc()): ?>
                    <div class="provider-item">
                        <div class="provider-header">
                            <div>
                                <h3><?php echo htmlspecialchars($provider['provider_name']); ?></h3>
                                <p style="color: var(--text-light);"><?php echo htmlspecialchars($provider['category_name']); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $provider['account_status']; ?>">
                                <?php echo ucfirst($provider['account_status']); ?>
                            </span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                            <div><strong>Email:</strong> <?php echo htmlspecialchars($provider['email']); ?></div>
                            <div><strong>Phone:</strong> <?php echo htmlspecialchars($provider['phone']); ?></div>
                            <div><strong>Experience:</strong> <?php echo $provider['experience_years']; ?> years</div>
                        </div>

                        <div style="margin: 1rem 0;">
                            <strong>Bio:</strong> <?php echo htmlspecialchars(substr($provider['bio'], 0, 200)); ?>...
                        </div>

                        <div style="margin: 1rem 0;">
                            <strong>Skills:</strong> <?php echo htmlspecialchars($provider['skills']); ?>
                        </div>

                        <div style="display: flex; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                            <?php if($provider['account_status'] == 'pending'): ?>
                                <form method="POST" action="admin-update-provider.php" style="display: inline;">
                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" action="admin-update-provider.php" style="display: inline;">
                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                    <input type="hidden" name="status" value="suspended">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this provider?')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            <?php elseif($provider['account_status'] == 'active'): ?>
                                <button onclick="openSuspendModal(<?php echo $provider['provider_id']; ?>, '<?php echo htmlspecialchars($provider['provider_name']); ?>')" class="btn btn-warning">
                                    <i class="fas fa-ban"></i> Suspend
                                </button>
                            <?php else: ?>
                                <form method="POST" action="admin-update-provider.php" style="display: inline;">
                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Activate
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-light);"></i>
                        <p style="margin-top: 1rem;">No providers found</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Suspension Modal -->
    <div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <h3 style="margin-bottom: 1rem; color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i> Suspend Provider
            </h3>
            <p style="margin-bottom: 1.5rem; color: #6b7280;">
                You are about to suspend <strong id="providerNameDisplay"></strong>
            </p>
            
            <form method="POST" action="admin-suspend-provider.php">
                <input type="hidden" name="provider_id" id="suspendProviderId">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #1f2937;">
                        Suspension Reason *
                    </label>
                    <textarea name="suspension_reason" rows="4" required 
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem;"
                        placeholder="Enter reason for suspension (e.g., Multiple customer complaints, Violation of terms, etc.)"></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #1f2937;">
                        Action Required from Provider (Optional)
                    </label>
                    <input type="text" name="action_required" 
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem;"
                        placeholder="e.g., Contact support, Submit documents, etc.">
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" style="flex: 1; padding: 0.75rem; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif;">
                        <i class="fas fa-ban"></i> Suspend Provider
                    </button>
                    <button type="button" onclick="closeSuspendModal()" style="flex: 1; padding: 0.75rem; background: #6b7280; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openSuspendModal(providerId, providerName) {
        document.getElementById('suspendProviderId').value = providerId;
        document.getElementById('providerNameDisplay').textContent = providerName;
        document.getElementById('suspendModal').style.display = 'flex';
    }

    function closeSuspendModal() {
        document.getElementById('suspendModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if(e.target === this) {
            closeSuspendModal();
        }
    });
    </script>

</body>
</html>
