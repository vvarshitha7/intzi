<?php 
require_once 'config.php';
requireProviderLogin();

$provider_id = $_SESSION['provider_id'];

// Get provider details
$provider_sql = "SELECT sp.*, sc.category_name 
                FROM service_providers sp 
                JOIN service_categories sc ON sp.category_id = sc.category_id 
                WHERE sp.provider_id = $provider_id";
$provider_result = $conn->query($provider_sql);
$provider = $provider_result->fetch_assoc();

$success = '';
$error = '';

// Add new catalog item
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $service_description = $conn->real_escape_string($_POST['service_description']);
    $price = (float)$_POST['price'];
    $duration_hours = (int)$_POST['duration_hours'];
    $duration_minutes = (int)$_POST['duration_minutes'];
    
    // Calculate total duration in minutes
    $total_duration_minutes = ($duration_hours * 60) + $duration_minutes;
    
    if($total_duration_minutes < 30) {
        $error = "Service duration must be at least 30 minutes";
    } else {
        $sql = "INSERT INTO provider_catalog (provider_id, service_name, service_description, price, duration_minutes) 
                VALUES ($provider_id, '$service_name', '$service_description', $price, $total_duration_minutes)";
        
        if($conn->query($sql)) {
            // Update provider price range
            updateProviderPriceRange($conn, $provider_id);
            $success = "Service added to catalog successfully!";
        } else {
            $error = "Failed to add service. Please try again.";
        }
    }
}

// Delete catalog item
if(isset($_GET['delete'])) {
    $catalog_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM provider_catalog WHERE catalog_id = $catalog_id AND provider_id = $provider_id");
    updateProviderPriceRange($conn, $provider_id);
    header("Location: provider-catalog.php?success=deleted");
    exit();
}

// Toggle active status
if(isset($_GET['toggle'])) {
    $catalog_id = (int)$_GET['toggle'];
    $conn->query("UPDATE provider_catalog SET is_active = NOT is_active WHERE catalog_id = $catalog_id AND provider_id = $provider_id");
    header("Location: provider-catalog.php");
    exit();
}

// Function to update provider price range
function updateProviderPriceRange($conn, $provider_id) {
    $sql = "UPDATE service_providers sp
            SET min_price = (SELECT MIN(price) FROM provider_catalog WHERE provider_id = $provider_id AND is_active = 1),
                max_price = (SELECT MAX(price) FROM provider_catalog WHERE provider_id = $provider_id AND is_active = 1)
            WHERE provider_id = $provider_id";
    $conn->query($sql);
}

// Get all catalog items
$catalog_sql = "SELECT * FROM provider_catalog WHERE provider_id = $provider_id ORDER BY created_at DESC";
$catalog_result = $conn->query($catalog_sql);

if(isset($_GET['success']) && $_GET['success'] == 'deleted') {
    $success = "Service deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Service Catalog - Intzi</title>
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

        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 1rem;
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .catalog-item {
            background: white;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            position: relative;
        }

        .catalog-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .catalog-item.inactive {
            opacity: 0.6;
            background: #f9fafb;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #eff6ff;
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .price-display {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--success);
            margin: 1rem 0;
        }

        .info-box {
            background: #eff6ff;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-blue);
        }

        small {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
            .form-row-3 {
                grid-template-columns: 1fr;
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
                <li><a href="provider-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="provider-catalog.php" class="active"><i class="fas fa-list"></i> My Services</a></li>
                <li><a href="provider-bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="provider-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="provider-earnings.php"><i class="fas fa-rupee-sign"></i> Earnings</a></li>
                <li><a href="provider-reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="provider-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-list"></i> My Service Catalog</h1>
                <p style="color: var(--text-light);">Manage your services, pricing, and duration</p>
                <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 0.5rem;">
                    <strong>Category:</strong> <?php echo htmlspecialchars($provider['category_name']); ?>
                </p>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <strong><i class="fas fa-info-circle"></i> How it works:</strong>
                <p style="margin-top: 0.5rem;">Create services with fixed prices and durations. When customers book, they'll select a service and choose a start time - the end time is automatically calculated!</p>
            </div>

            <!-- Add New Service Form -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-plus-circle"></i> Add New Service</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Service Name *</label>
                        <input type="text" name="service_name" required placeholder="e.g., Designer Blouse Stitching, Bridal Makeup">
                    </div>

                    <div class="form-group">
                        <label>Service Description *</label>
                        <textarea name="service_description" rows="3" required placeholder="Describe what's included in this service..."></textarea>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" required min="0" step="0.01" placeholder="e.g., 800">
                        </div>
                        <div class="form-group">
                            <label>Hours *</label>
                            <select name="duration_hours" required>
                                <option value="0">0</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Minutes *</label>
                            <select name="duration_minutes" required>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                        </div>
                    </div>

                    <small style="display: block; margin-top: -1rem; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle"></i> Minimum duration: 30 minutes
                    </small>

                    <button type="submit" name="add_service" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Service to Catalog
                    </button>
                </form>
            </div>

            <!-- Catalog Items -->
            <h3 style="margin-bottom: 1rem;">Your Services (<?php echo $catalog_result->num_rows; ?>)</h3>
            <?php if($catalog_result->num_rows > 0): ?>
                <div class="catalog-grid">
                    <?php while($item = $catalog_result->fetch_assoc()): 
                        $hours = floor($item['duration_minutes'] / 60);
                        $minutes = $item['duration_minutes'] % 60;
                    ?>
                    <div class="catalog-item <?php echo $item['is_active'] ? '' : 'inactive'; ?>">
                        <div class="category-badge">
                            <i class="fas fa-<?php echo $item['is_active'] ? 'check-circle' : 'times-circle'; ?>"></i>
                            <?php echo $item['is_active'] ? 'Active' : 'Inactive'; ?>
                        </div>

                        <h4 style="margin-bottom: 0.5rem; padding-right: 100px;"><?php echo htmlspecialchars($item['service_name']); ?></h4>
                        
                        <p style="color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.6;">
                            <?php echo htmlspecialchars($item['service_description']); ?>
                        </p>

                        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-light); margin-bottom: 1rem;">
                            <i class="fas fa-clock"></i>
                            <strong>Duration:</strong>
                            <?php if($hours > 0) echo $hours . ' hour' . ($hours > 1 ? 's' : ''); ?>
                            <?php if($minutes > 0) echo ' ' . $minutes . ' mins'; ?>
                        </div>

                        <div class="price-display">
                            ₹<?php echo number_format($item['price'], 2); ?>
                        </div>

                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="?toggle=<?php echo $item['catalog_id']; ?>" 
                               class="btn <?php echo $item['is_active'] ? 'btn-warning' : 'btn-success'; ?> btn-sm" 
                               style="flex: 1;">
                                <i class="fas fa-<?php echo $item['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                <?php echo $item['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </a>
                            <a href="?delete=<?php echo $item['catalog_id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this service?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>

                        <small style="display: block; margin-top: 1rem; text-align: center;">
                            Added on <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                        </small>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 3rem; color: var(--text-light);">
                    <i class="fas fa-list" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <h3>No services in your catalog yet</h3>
                    <p>Add your first service above to start receiving bookings!</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
