<?php 
require_once 'config.php';
requireAdminLogin();

$success = isset($_SESSION['admin_success']) ? $_SESSION['admin_success'] : '';
unset($_SESSION['admin_success']);

// Handle add/edit/delete
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_category'])) {
        $name = sanitize($_POST['category_name']);
        $desc = sanitize($_POST['description']);
        $icon = sanitize($_POST['category_icon']);
        
        $sql = "INSERT INTO service_categories (category_name, description, category_icon) VALUES ('$name', '$desc', '$icon')";
        if($conn->query($sql)) {
            $_SESSION['admin_success'] = "Category added successfully!";
            header("Location: admin-categories.php");
            exit();
        }
    }
}

$categories_result = $conn->query("SELECT * FROM service_categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
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
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            padding: 1.5rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            text-align: center;
        }

        .category-icon {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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
                <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="admin-providers.php"><i class="fas fa-user-tie"></i> Providers</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-bookings.php"><i class="fas fa-calendar"></i> Bookings</a></li>
                <li><a href="admin-categories.php" class="active"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Service Categories Management</h1>

            <?php if($success): ?>
                <div class="alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Add New Category</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="category_name" required placeholder="e.g., Tailoring">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required placeholder="Brief description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Font Awesome Icon Class</label>
                        <input type="text" name="category_icon" required placeholder="e.g., fa-cut">
                        <small style="color: var(--text-light);">Find icons at fontawesome.com</small>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Existing Categories</h2>
                <div class="categories-grid">
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas <?php echo htmlspecialchars($cat['category_icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($cat['category_name']); ?></h3>
                        <p style="color: var(--text-light); font-size: 0.9rem;"><?php echo htmlspecialchars($cat['description']); ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
