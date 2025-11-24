<?php 
require_once 'config.php';

// Fetch categories
$categories_query = "SELECT * FROM service_categories";
$categories_result = $conn->query($categories_query);

// Fetch featured providers
$featured_query = "SELECT sp.*, sc.category_name 
                   FROM service_providers sp 
                   JOIN service_categories sc ON sp.category_id = sc.category_id 
                   WHERE sp.rating >= 4.5 
                   ORDER BY sp.rating DESC, sp.total_bookings DESC 
                   LIMIT 6";
$featured_result = $conn->query($featured_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intzi - Your Local Service Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Root Variables - Blue Theme */
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --light-blue: #dbeafe;
            --dark-blue: #1e3a8a;
            --accent-blue: #3b82f6;
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
            transition: all 0.3s ease;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Header & Navigation */
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
            transition: color 0.3s;
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

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 4rem 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .search-box {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .search-box input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
        }

        .search-box button {
            padding: 1rem 2rem;
            background: var(--primary-blue);
            color: var(--white);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .search-box button:hover {
            background: var(--secondary-blue);
        }

        /* Section Styles */
        .section {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--text-dark);
            font-weight: 700;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            font-size: 1.1rem;
            margin-top: -2rem;
            margin-bottom: 3rem;
        }

        /* Category Cards */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .category-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.15);
        }

        .category-icon {
            font-size: 3.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .category-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .category-card p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* Provider Cards */
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

        /* Footer */
        .footer {
            background: var(--dark-blue);
            color: var(--white);
            padding: 3rem 0 1rem;
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

        .footer-section a:hover {
            color: var(--white);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .search-box {
                flex-direction: column;
            }

            .section-title {
                font-size: 2rem;
            }

            .categories-grid,
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Find Local Services in Minutes</h1>
            <p>Connect with trusted professionals for tailoring, beauty, catering, and household services</p>
            <div class="search-box">
                <input type="text" placeholder="Search for services or professionals..." id="searchInput">
                <button onclick="searchServices()"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Choose from our wide range of local services</p>
            
            <div class="categories-grid">
                <?php while($category = $categories_result->fetch_assoc()): ?>
                <div class="category-card" onclick="window.location.href='services.php?category=<?php echo $category['category_id']; ?>'">
                    <div class="category-icon">
                        <i class="fas <?php echo htmlspecialchars($category['category_icon']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Featured Providers -->
    <section class="section" style="background: white;">
        <div class="container">
            <h2 class="section-title">Top-Rated Professionals</h2>
            <p class="section-subtitle">Trusted by hundreds of customers in Hyderabad</p>
            
            <div class="providers-grid">
                <?php while($provider = $featured_result->fetch_assoc()): ?>
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
                        <p class="provider-bio"><?php echo htmlspecialchars(substr($provider['bio'], 0, 80)) . '...'; ?></p>
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
        </div>
    </section>

    <!-- How It Works -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">How Intzi Works</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon"><i class="fas fa-search"></i></div>
                    <h3>Browse Services</h3>
                    <p>Explore our wide range of local service providers</p>
                </div>
                <div class="category-card">
                    <div class="category-icon"><i class="fas fa-calendar-check"></i></div>
                    <h3>Book Instantly</h3>
                    <p>Schedule services for now or later at your convenience</p>
                </div>
                <div class="category-card">
                    <div class="category-icon"><i class="fas fa-star"></i></div>
                    <h3>Get Quality Service</h3>
                    <p>Enjoy professional services from verified providers</p>
                </div>
                <div class="category-card">
                    <div class="category-icon"><i class="fas fa-credit-card"></i></div>
                    <h3>Secure Payment</h3>
                    <p>Pay safely through our integrated payment gateway</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-hands-helping"></i> Intzi</h3>
                    <p>Empowering local service providers and connecting communities through dignified work opportunities.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="services.php">Browse Services</a>
                    <a href="register.php">Become a Provider</a>
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                </div>
                <div class="footer-section">
                    <h3>Services</h3>
                    <a href="services.php?category=1">Tailoring</a>
                    <a href="services.php?category=2">Beauty Services</a>
                    <a href="services.php?category=3">Food & Catering</a>
                    <a href="services.php?category=4">Household Help</a>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"></i> support@intzi.com</p>
                    <p><i class="fas fa-phone"></i> +91 9876543210</p>
                    <p><i class="fas fa-map-marker-alt"></i> Hyderabad, Telangana</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Intzi. All rights reserved. | Empowering Local Communities</p>
            </div>
        </div>
    </footer>

    <script>
        function searchServices() {
            const query = document.getElementById('searchInput').value;
            window.location.href = 'services.php?search=' + encodeURIComponent(query);
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                searchServices();
            }
        });

        function bookProvider(providerId) {
            <?php if(isLoggedIn()): ?>
                window.location.href = 'booking.php?provider=' + providerId;
            <?php else: ?>
                window.location.href = 'login.php?redirect=' + encodeURIComponent('booking.php?provider=' + providerId);
            <?php endif; ?>
        }
    </script>
</body>
</html>
