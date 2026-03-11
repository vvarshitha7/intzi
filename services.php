<?php 
require_once 'config.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build SQL query
$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        LEFT JOIN service_categories sc ON sp.category_id = sc.category_id 
        WHERE sp.account_status = 'active'";

if($category_id > 0) {
    $sql .= " AND sp.category_id = $category_id";
}

if(!empty($search)) {
    $sql .= " AND (sp.provider_name LIKE '%$search%' OR sp.bio LIKE '%$search%' OR sp.skills LIKE '%$search%')";
}

$sql .= " ORDER BY sp.rating DESC, sp.total_reviews DESC";

$providers_result = $conn->query($sql);

// Get all categories for filter
$categories = $conn->query("SELECT * FROM service_categories ORDER BY category_name");
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
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
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
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Header */
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

        .section {
            padding: 3rem 0;
        }

        /* Search & Filter Section */
        .search-section {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 100;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: stretch;
        }

        .autocomplete-wrapper {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .autocomplete-wrapper input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .autocomplete-wrapper input:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .search-form select {
            width: 200px;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .search-form select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .location-box {
            padding: 0.9rem 1rem;
            background: #eff6ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .search-form .btn {
            flex-shrink: 0;
        }

        .autocomplete-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border: 2px solid var(--primary-blue);
            border-radius: 8px;
            max-height: 350px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15);
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 0.8rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background: #eff6ff;
        }

        .autocomplete-item i {
            color: var(--primary-blue);
            width: 18px;
            font-size: 0.9rem;
        }

        .autocomplete-category {
            padding: 0.6rem 1rem;
            background: #f3f4f6;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .highlight {
            font-weight: 700;
            color: var(--primary-blue);
            background: #eff6ff;
            padding: 0 2px;
        }

        .no-results {
            padding: 2rem 1rem;
            text-align: center;
            color: var(--text-light);
        }

        .no-results i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
            opacity: 0.5;
        }

        /* Results Header */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        /* Provider Grid */
        .providers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .provider-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .provider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .provider-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: var(--bg-light);
        }

        .provider-content {
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
            margin-bottom: 0.3rem;
        }

        .provider-category {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .availability-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-available {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-busy {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-unavailable {
            background: #fee2e2;
            color: #991b1b;
        }

        .rating {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .rating i {
            color: var(--warning);
            margin-right: 2px;
        }

        .rating-text {
            margin-left: 0.5rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .rating-number {
            font-weight: 700;
            color: var(--text-dark);
        }

        .provider-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .provider-info span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .provider-bio {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .provider-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .skill-tag {
            background: #eff6ff;
            color: var(--primary-blue);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
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
            color: var(--success);
        }

        .price-label {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 400;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 0;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }

            .search-form {
                flex-direction: column;
            }

            .autocomplete-wrapper,
            .search-form select,
            .location-box {
                width: 100%;
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

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <h1 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem;">Browse Service Providers</h1>

            <!-- Search & Filter -->
            <div class="search-section">
                <form method="GET" action="" class="search-form" id="searchForm">
                    <div class="autocomplete-wrapper">
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               placeholder="Search by name, skills, or service..." 
                               value="<?php echo htmlspecialchars($search); ?>"
                               autocomplete="off">
                        <div class="autocomplete-dropdown" id="autocompleteDropdown"></div>
                    </div>
                    
                    <select name="category" id="categorySelect">
                        <option value="">All Categories</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo $category_id == $cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <div class="location-box">
                        <i class="fas fa-map-marker-alt" style="color: var(--primary-blue);"></i>
                        <span><strong>Location:</strong> Hyderabad, Telangana</span>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <h2>
                    <?php 
                    $total = $providers_result->num_rows;
                    echo $total . ' Provider' . ($total != 1 ? 's' : '') . ' Found';
                    ?>
                </h2>
            </div>

            <!-- Providers Grid -->
            <?php if($providers_result->num_rows > 0): ?>
                <div class="providers-grid">
                    <?php while($provider = $providers_result->fetch_assoc()): ?>
                    <div class="provider-card" onclick="window.location.href='service-details.php?id=<?php echo $provider['provider_id']; ?>'">
                        <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                             alt="<?php echo htmlspecialchars($provider['provider_name']); ?>"
                             class="provider-image"
                             onerror="this.src='images/providers/default-provider.jpg'">
                        
                        <div class="provider-content">
                            <div class="provider-header">
                                <div>
                                    <div class="provider-name"><?php echo htmlspecialchars($provider['provider_name']); ?></div>
                                    <div class="provider-category">
                                        <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($provider['category_name']); ?>
                                    </div>
                                </div>
                                <?php
                                $availability = $provider['availability_status'];
                                $badge_class = 'badge-' . $availability;
                                $badge_icon = $availability == 'available' ? 'check' : ($availability == 'busy' ? 'clock' : 'times');
                                ?>
                                <span class="availability-badge <?php echo $badge_class; ?>">
                                    <i class="fas fa-<?php echo $badge_icon; ?>"></i> <?php echo ucfirst($availability); ?>
                                </span>
                            </div>

                            <div class="rating">
                                <?php 
                                $rating = isset($provider['rating']) ? (float)$provider['rating'] : 0;
                                $total_reviews = isset($provider['total_reviews']) ? (int)$provider['total_reviews'] : 0;
                                
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= floor($rating)) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif($i == ceil($rating) && ($rating - floor($rating)) >= 0.5) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                                <span class="rating-text">
                                    <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                                    <?php if($total_reviews > 0): ?>
                                        (<?php echo $total_reviews; ?> <?php echo $total_reviews == 1 ? 'review' : 'reviews'; ?>)
                                    <?php else: ?>
                                        (No reviews yet)
                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class="provider-info">
                                <span>
                                    <i class="fas fa-calendar-alt"></i> 
                                    <?php echo $provider['experience_years']; ?> years exp.
                                </span>
                                <span>
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($provider['city']); ?>
                                </span>
                            </div>

                            <p class="provider-bio"><?php echo htmlspecialchars($provider['bio']); ?></p>

                            <div class="provider-skills">
                                <?php 
                                $skills = explode(',', $provider['skills']);
                                $displayed_skills = array_slice($skills, 0, 3);
                                foreach($displayed_skills as $skill): 
                                ?>
                                    <span class="skill-tag"><?php echo trim(htmlspecialchars($skill)); ?></span>
                                <?php endforeach; ?>
                                <?php if(count($skills) > 3): ?>
                                    <span class="skill-tag">+<?php echo count($skills) - 3; ?> more</span>
                                <?php endif; ?>
                            </div>

                            <div class="provider-footer">
                                <div>
                                    <?php if(!empty($provider['min_price']) && !empty($provider['max_price'])): ?>
                                        <?php if($provider['min_price'] == $provider['max_price']): ?>
                                            <div class="price">₹<?php echo number_format($provider['min_price'], 0); ?></div>
                                            <div class="price-label">per service</div>
                                        <?php else: ?>
                                            <div class="price">₹<?php echo number_format($provider['min_price'], 0); ?> - ₹<?php echo number_format($provider['max_price'], 0); ?></div>
                                            <div class="price-label">price range</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="price" style="font-size: 1rem; color: var(--text-light);">Price on request</div>
                                        <div class="price-label">Contact for pricing</div>
                                    <?php endif; ?>
                                </div>
                                <a href="service-details.php?id=<?php echo $provider['provider_id']; ?>" 
                                   class="btn btn-primary" 
                                   onclick="event.stopPropagation();">
                                    View Profile <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No providers found</h3>
                    <p>Try adjusting your search filters to find what you're looking for.</p>
                    <a href="services.php" class="btn btn-primary" style="margin-top: 1rem;">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Autocomplete functionality
        const searchInput = document.getElementById('searchInput');
        const autocompleteDropdown = document.getElementById('autocompleteDropdown');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if(query.length < 2) {
                autocompleteDropdown.classList.remove('show');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            if(this.value.trim().length >= 2) {
                fetchSuggestions(this.value.trim());
            }
        });

        document.addEventListener('click', function(e) {
            if(!searchInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
                autocompleteDropdown.classList.remove('show');
            }
        });

        function fetchSuggestions(query) {
            fetch(`autocomplete.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySuggestions(data, query);
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                });
        }

        function displaySuggestions(data, query) {
            let html = '';

            if(data.providers && data.providers.length > 0) {
                html += '<div class="autocomplete-category">Providers</div>';
                data.providers.forEach(provider => {
                    const highlightedName = highlightMatch(provider.name, query);
                    html += `
                        <div class="autocomplete-item" onclick="selectSuggestion('${escapeHtml(provider.name)}')">
                            <i class="fas fa-user"></i>
                            <span>${highlightedName}</span>
                        </div>
                    `;
                });
            }

            if(data.skills && data.skills.length > 0) {
                html += '<div class="autocomplete-category">Skills</div>';
                data.skills.forEach(skill => {
                    const highlightedSkill = highlightMatch(skill, query);
                    html += `
                        <div class="autocomplete-item" onclick="selectSuggestion('${escapeHtml(skill)}')">
                            <i class="fas fa-tools"></i>
                            <span>${highlightedSkill}</span>
                        </div>
                    `;
                });
            }

            if(data.categories && data.categories.length > 0) {
                html += '<div class="autocomplete-category">Categories</div>';
                data.categories.forEach(category => {
                    const highlightedCategory = highlightMatch(category, query);
                    html += `
                        <div class="autocomplete-item" onclick="selectSuggestion('${escapeHtml(category)}')">
                            <i class="fas fa-briefcase"></i>
                            <span>${highlightedCategory}</span>
                        </div>
                    `;
                });
            }

            if(html === '') {
                html = '<div class="no-results"><i class="fas fa-search"></i><br>No suggestions found</div>';
            }

            autocompleteDropdown.innerHTML = html;
            autocompleteDropdown.classList.add('show');
        }

        function highlightMatch(text, query) {
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        function selectSuggestion(value) {
            searchInput.value = value;
            autocompleteDropdown.classList.remove('show');
            document.getElementById('searchForm').submit();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function escapeRegex(text) {
            return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        searchInput.addEventListener('keydown', function(e) {
            const items = autocompleteDropdown.querySelectorAll('.autocomplete-item');
            const activeItem = autocompleteDropdown.querySelector('.autocomplete-item:hover');
            let index = Array.from(items).indexOf(activeItem);

            if(e.key === 'ArrowDown') {
                e.preventDefault();
                index = index < items.length - 1 ? index + 1 : 0;
                items[index]?.scrollIntoView({ block: 'nearest' });
                items.forEach(item => item.style.background = '');
                items[index].style.background = '#eff6ff';
            } else if(e.key === 'ArrowUp') {
                e.preventDefault();
                index = index > 0 ? index - 1 : items.length - 1;
                items[index]?.scrollIntoView({ block: 'nearest' });
                items.forEach(item => item.style.background = '');
                items[index].style.background = '#eff6ff';
            } else if(e.key === 'Enter' && items.length > 0) {
                const highlighted = Array.from(items).find(item => item.style.background);
                if(highlighted) {
                    e.preventDefault();
                    highlighted.click();
                }
            } else if(e.key === 'Escape') {
                autocompleteDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
