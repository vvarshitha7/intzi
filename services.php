<?php 
require_once 'config.php';

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'rating';

// Build SQL query
$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        JOIN service_categories sc ON sp.category_id = sc.category_id 
        WHERE 1=1";

if($category_filter > 0) {
    $sql .= " AND sp.category_id = $category_filter";
}

if($search_query != '') {
    $sql .= " AND (sp.provider_name LIKE '%$search_query%' 
              OR sp.skills LIKE '%$search_query%' 
              OR sc.category_name LIKE '%$search_query%')";
}

// Sorting
switch($sort_by) {
    case 'rating':
        $sql .= " ORDER BY sp.rating DESC";
        break;
    case 'price_low':
        $sql .= " ORDER BY sp.hourly_rate ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY sp.hourly_rate DESC";
        break;
    case 'bookings':
        $sql .= " ORDER BY sp.total_bookings DESC";
        break;
    default:
        $sql .= " ORDER BY sp.rating DESC";
}

$providers_result = $conn->query($sql);

// Get all categories for filter
$categories_query = "SELECT * FROM service_categories";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Services - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --light-blue: #dbeafe;
            --dark-blue: #1e3a8a;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
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
            transition: all 0.3s ease;
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

        .nav-links a:hover {
            color: var(--primary-blue);
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

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary-blue);
            color: var(--white);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .section {
            padding: 3rem 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--text-dark);
            font-weight: 700;
        }

        .filter-section {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .providers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .provider-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .provider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .provider-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: var(--light-blue);
        }

        .provider-info {
            padding: 1.5rem;
        }

        .provider-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .provider-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .provider-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            background: var(--success);
            color: var(--white);
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .provider-bio {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .provider-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .provider-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .skill-tag {
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .provider-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .price-label {
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 400;
        }

        .footer {
            background: var(--dark-blue);
            color: var(--white);
            padding: 2rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.8;
            display: block;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
            .filter-grid {
                grid-template-columns: 1fr;
            }
            .providers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-hands-helping"></i> Intzi
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if(isLoggedIn()): ?>
                    <li><a href="my-bookings.php">My Bookings</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="section">
        <div class="container">
            <h1 class="section-title">Browse Service Providers</h1>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="services.php" id="filterForm">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" onchange="document.getElementById('filterForm').submit()">
                                <option value="0">All Categories</option>
                                <?php 
                                $categories_result->data_seek(0);
                                while($cat = $categories_result->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $cat['category_id']; ?>" 
                                        <?php echo ($category_filter == $cat['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sort By</label>
                            <select name="sort" onchange="document.getElementById('filterForm').submit()">
                                <option value="rating" <?php echo ($sort_by == 'rating') ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="price_low" <?php echo ($sort_by == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo ($sort_by == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="bookings" <?php echo ($sort_by == 'bookings') ? 'selected' : ''; ?>>Most Booked</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" placeholder="Search providers..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results -->
            <div style="margin: 2rem 0;">
                <p style="color: var(--text-light);">
                    Showing <?php echo $providers_result->num_rows; ?> provider(s)
                    <?php if($search_query): ?>
                        for "<?php echo htmlspecialchars($search_query); ?>"
                    <?php endif; ?>
                </p>
            </div>

            <!-- Providers Grid -->
            <?php if($providers_result->num_rows > 0): ?>
            <div class="providers-grid">
                <?php while($provider = $providers_result->fetch_assoc()): ?>
                <div class="provider-card" onclick="window.location.href='service-details.php?id=<?php echo $provider['provider_id']; ?>'">
                    <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                         alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                         class="provider-image"
                         onerror="this.src='images/default-provider.jpg'">
                    <div class="provider-info">
                        <div class="provider-header">
                            <h3 class="provider-name"><?php echo htmlspecialchars($provider['provider_name']); ?></h3>
                            <div class="provider-rating">
                                <i class="fas fa-star"></i> <?php echo $provider['rating']; ?>
                            </div>
                        </div>
                        <span class="skill-tag" style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($provider['category_name']); ?></span>
                        <p class="provider-bio"><?php echo htmlspecialchars(substr($provider['bio'], 0, 100)) . '...'; ?></p>
                        <div class="provider-meta">
                            <span class="meta-item">
                                <i class="fas fa-briefcase"></i> <?php echo $provider['experience_years']; ?> years
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($provider['city']); ?>
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-check-circle"></i> <?php echo $provider['total_bookings']; ?> bookings
                            </span>
                        </div>
                        <?php 
                        $skills = explode(',', $provider['skills']);
                        $displaySkills = array_slice($skills, 0, 3);
                        ?>
                        <div class="provider-skills">
                            <?php foreach($displaySkills as $skill): ?>
                            <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="provider-footer">
                            <div class="price">
                                ₹<?php echo number_format($provider['hourly_rate'], 0); ?>
                                <span class="price-label">/hour</span>
                            </div>
                            <button class="btn btn-primary" onclick="event.stopPropagation(); bookProvider(<?php echo $provider['provider_id']; ?>)">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3>No providers found</h3>
                <p style="color: var(--text-light);">Try adjusting your filters or search terms</p>
                <a href="services.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Clear Filters</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Intzi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function bookProvider(providerId) {
            <?php if(isLoggedIn()): ?>
                window.location.href = 'booking.php?provider=' + providerId;
            <?php else: ?>
                alert('Please login to book a service');
                window.location.href = 'login.php?redirect=' + encodeURIComponent('booking.php?provider=' + providerId);
            <?php endif; ?>
        }
    </script>
</body>
</html>
