<?php 
require_once 'config.php';
require_once 'hyderabad-data.php';

$provider_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($provider_id == 0) {
    echo "<script>window.location.href='services.php';</script>";
    exit();
}

$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        LEFT JOIN service_categories sc ON sp.category_id = sc.category_id 
        WHERE sp.provider_id = $provider_id AND sp.account_status = 'active'";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    echo "<script>window.location.href='services.php';</script>";
    exit();
}

$provider = $result->fetch_assoc();

// Get provider's catalog
$catalog_sql = "SELECT * FROM provider_catalog WHERE provider_id = $provider_id AND is_active = 1 ORDER BY price ASC";
$catalog_result = $conn->query($catalog_sql);

$user_address = null;
if(isLoggedIn()) {
    $user_sql = "SELECT house_number, street, area, landmark, pincode FROM users WHERE user_id = {$_SESSION['user_id']}";
    $user_result = $conn->query($user_sql);
    if($user_result->num_rows > 0) {
        $user_address = $user_result->fetch_assoc();
    }
}

$error = '';

// BOOKING SUBMISSION - UPDATED WITH SERVICE SELECTION
if($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $booking_date = $conn->real_escape_string($_POST['booking_date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $catalog_item_id = (int)$_POST['catalog_item_id'];
    $special_requests = isset($_POST['special_requests']) ? $conn->real_escape_string($_POST['special_requests']) : '';
    
    if($catalog_item_id == 0) {
        $error = "Please select a service";
    } else {
        // Get service details
        $service_sql = "SELECT price, duration_minutes, service_name FROM provider_catalog WHERE catalog_id = $catalog_item_id";
        $service_result = $conn->query($service_sql);
        $service = $service_result->fetch_assoc();
        
        // Calculate end time based on service duration
        $start_datetime = new DateTime($booking_date . ' ' . $start_time);
        $end_datetime = clone $start_datetime;
        $end_datetime->add(new DateInterval('PT' . $service['duration_minutes'] . 'M'));
        $end_time = $end_datetime->format('H:i:s');
        
        // Validation: Check if booking time is not in the past
        $booking_datetime = strtotime($booking_date . ' ' . $start_time);
        $current_datetime = time();
        
        if($booking_datetime <= $current_datetime) {
            $error = "You cannot book for past dates or times. Please select a future time.";
        }
        
        if(empty($error)) {
            $use_saved_address = isset($_POST['use_saved_address']) && $_POST['use_saved_address'] == '1';
            
            if($use_saved_address && $user_address) {
                $house_number = $user_address['house_number'];
                $street = $user_address['street'];
                $area = $user_address['area'];
                $landmark = $user_address['landmark'];
                $pincode = $user_address['pincode'];
            } else {
                $house_number = $conn->real_escape_string($_POST['house_number']);
                $street = $conn->real_escape_string($_POST['street']);
                $area = $conn->real_escape_string($_POST['area']);
                $landmark = isset($_POST['landmark']) ? $conn->real_escape_string($_POST['landmark']) : '';
                $pincode = $conn->real_escape_string($_POST['pincode']);
            }
            
            $service_amount = $service['price'];
            $platform_fee = 50;
            $total_amount = $service_amount + $platform_fee;
            
            $insert_sql = "INSERT INTO bookings 
                          (user_id, provider_id, category_id, catalog_item_id, booking_date, booking_time, 
                           start_time, end_time, total_amount, house_number, street, area, landmark, pincode,
                           use_registered_address, special_requests, booking_status, payment_status, created_at) 
                          VALUES 
                          ($user_id, $provider_id, {$provider['category_id']}, $catalog_item_id, 
                           '$booking_date', '$start_time', '$start_time', '$end_time',
                           $total_amount, '$house_number', '$street', 
                           '$area', '$landmark', '$pincode', " . ($use_saved_address ? 1 : 0) . ",
                           '$special_requests', 'pending', 'pending', NOW())";
            
            if($conn->query($insert_sql)) {
                $booking_id = $conn->insert_id;
                
                // Set session variables for payment page
                $_SESSION['booking_id'] = $booking_id;
                $_SESSION['payment_amount'] = $total_amount;
                $_SESSION['provider_name'] = $provider['provider_name'];
                $_SESSION['category_name'] = $provider['category_name'];
                $_SESSION['service_name'] = $service['service_name'];
                
                // Redirect to payment
                header("Location: payment.php?booking_id=$booking_id");
                exit();
            } else {
                $error = "Booking failed. Please try again.";
            }
        }
    }
}

// Get reviews
$reviews_sql = "SELECT r.*, u.full_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.provider_id = $provider_id 
                ORDER BY r.created_at DESC 
                LIMIT 10";
$reviews_result = $conn->query($reviews_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($provider['provider_name']); ?> - Intzi</title>
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
            line-height: 1.6;
        }
        a { text-decoration: none; color: inherit; }
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
        .nav-links a { color: var(--text-dark); font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: var(--primary-blue); }
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
        }
        .btn-primary { background: var(--primary-blue); color: var(--white); }
        .btn-primary:hover { background: var(--secondary-blue); }
        .btn-outline { border: 2px solid var(--primary-blue); color: var(--primary-blue); background: transparent; }
        .btn-outline:hover { background: var(--primary-blue); color: var(--white); }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 5%;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .provider-details, .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .provider-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .rating i { color: var(--warning); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }
        .price-badge {
            background: var(--success);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .service-details-box {
            background: #eff6ff;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 2px solid var(--primary-blue);
            display: none;
        }
        .end-time-display {
            background: #f0fdf4;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--success);
            display: none;
        }
        small { color: var(--text-light); font-size: 0.85rem; }
        @media (max-width: 968px) {
            .container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
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

    <div class="container">
        <div class="provider-details">
            <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                 alt="<?php echo htmlspecialchars($provider['provider_name']); ?>"
                 class="provider-image"
                 onerror="this.src='images/providers/default-provider.jpg'">

            <h1><?php echo htmlspecialchars($provider['provider_name']); ?></h1>
            <p style="color: var(--text-light); font-size: 1.1rem;">
                <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($provider['category_name']); ?>
            </p>

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
                <span style="font-weight: 600;"><?php echo number_format($rating, 1); ?></span>
                <span style="color: var(--text-light);">(<?php echo $total_reviews; ?> reviews)</span>
            </div>

            <div style="background: var(--bg-light); padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                    <div>
                        <i class="fas fa-calendar-alt" style="color: var(--primary-blue); font-size: 1.5rem;"></i>
                        <p style="margin-top: 0.5rem; font-weight: 600;"><?php echo $provider['experience_years']; ?> Years</p>
                        <small>Experience</small>
                    </div>
                    <div>
                        <i class="fas fa-rupee-sign" style="color: var(--success); font-size: 1.5rem;"></i>
                        <p style="margin-top: 0.5rem; font-weight: 600;">₹<?php echo $provider['min_price']; ?> - ₹<?php echo $provider['max_price']; ?></p>
                        <small>Price Range</small>
                    </div>
                    <div>
                        <i class="fas fa-map-marker-alt" style="color: var(--danger); font-size: 1.5rem;"></i>
                        <p style="margin-top: 0.5rem; font-weight: 600;"><?php echo htmlspecialchars($provider['area']); ?></p>
                        <small>Location</small>
                    </div>
                </div>
            </div>

            <h3>About</h3>
            <p style="margin: 1rem 0;"><?php echo nl2br(htmlspecialchars($provider['bio'])); ?></p>

            <h3 style="margin-top: 2rem;">Available Services</h3>
            <?php if($catalog_result->num_rows > 0): ?>
                <div style="margin-top: 1rem;">
                    <?php while($catalog = $catalog_result->fetch_assoc()): 
                        $hours = floor($catalog['duration_minutes'] / 60);
                        $mins = $catalog['duration_minutes'] % 60;
                        $duration_text = '';
                        if($hours > 0) $duration_text .= $hours . ' hour' . ($hours > 1 ? 's' : '');
                        if($mins > 0) $duration_text .= ($hours > 0 ? ' ' : '') . $mins . ' mins';
                    ?>
                    <div style="border: 2px solid var(--border); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <strong style="font-size: 1.1rem; display: block; margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($catalog['service_name']); ?>
                                </strong>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($catalog['service_description']); ?>
                                </p>
                                <small style="display: flex; align-items: center; gap: 0.3rem;">
                                    <i class="fas fa-clock"></i> <?php echo $duration_text; ?>
                                </small>
                            </div>
                            <div style="text-align: right; margin-left: 1rem;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">
                                    ₹<?php echo number_format($catalog['price'], 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="background: #fee2e2; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid var(--danger);">
                    <p style="color: #991b1b;">
                        <i class="fas fa-exclamation-triangle"></i> No services listed yet. Please contact the provider directly.
                    </p>
                </div>
            <?php endif; ?>

            <h3 style="margin-top: 2rem;">Skills</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 1rem 0;">
                <?php 
                $skills = explode(',', $provider['skills']);
                foreach($skills as $skill): 
                ?>
                    <span style="background: #eff6ff; color: var(--primary-blue); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                        <?php echo trim(htmlspecialchars($skill)); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="booking-form">
            <h2 style="margin-bottom: 1.5rem;">Book This Service</h2>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if(!isLoggedIn()): ?>
                <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 8px;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                    <p style="margin-bottom: 1rem;">Please login to book this service</p>
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                </div>
            <?php else: ?>
                <?php
                $catalog_result2 = $conn->query("SELECT * FROM provider_catalog WHERE provider_id = $provider_id AND is_active = 1 ORDER BY price ASC");
                if($catalog_result2->num_rows > 0):
                ?>
                <form method="POST" action="" id="bookingForm">
                    <div class="form-group">
                        <label><i class="fas fa-list-check"></i> Select Service * <span style="color: var(--danger);">(Required)</span></label>
                        <select name="catalog_item_id" id="catalogSelect" required onchange="updateBookingDetails()">
                            <option value="">-- Choose a service --</option>
                            <?php while($cat = $catalog_result2->fetch_assoc()): 
                                $hours = floor($cat['duration_minutes'] / 60);
                                $mins = $cat['duration_minutes'] % 60;
                                $duration_text = '';
                                if($hours > 0) $duration_text .= $hours . 'h ';
                                if($mins > 0) $duration_text .= $mins . 'm';
                            ?>
                                <option value="<?php echo $cat['catalog_id']; ?>" 
                                        data-price="<?php echo $cat['price']; ?>"
                                        data-duration="<?php echo $cat['duration_minutes']; ?>"
                                        data-name="<?php echo htmlspecialchars($cat['service_name']); ?>"
                                        data-description="<?php echo htmlspecialchars($cat['service_description']); ?>">
                                    <?php echo htmlspecialchars($cat['service_name']); ?> - ₹<?php echo number_format($cat['price'], 0); ?> (<?php echo $duration_text; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Service Details Display -->
                    <div id="serviceDetailsBox" class="service-details-box">
                        <h4 style="color: var(--primary-blue); margin-bottom: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Selected Service
                        </h4>
                        <p style="color: var(--text-dark); margin-bottom: 0.5rem;">
                            <strong id="selectedServiceName"></strong>
                        </p>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.5rem;" id="selectedServiceDesc"></p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                            <div>
                                <i class="fas fa-clock"></i> <strong>Duration:</strong> <span id="selectedDuration"></span>
                            </div>
                            <div>
                                <i class="fas fa-rupee-sign"></i> <span id="selectedPrice" style="color: var(--success); font-size: 1.3rem; font-weight: 700;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Booking Date *</label>
                        <input type="date" name="booking_date" id="bookingDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Start Time *</label>
                        <input type="time" name="start_time" id="startTime" required>
                        <small style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> End time will be calculated automatically based on service duration
                        </small>
                    </div>

                    <!-- Auto-calculated End Time Display -->
                    <div id="endTimeDisplay" class="end-time-display">
                        <p style="color: #065f46; margin: 0;">
                            <i class="fas fa-check-circle"></i> <strong>Service will end at:</strong> 
                            <span id="calculatedEndTime" style="font-size: 1.2rem; font-weight: 700;"></span>
                        </p>
                    </div>

                    <div style="background: #eff6ff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Service Cost:</span>
                            <strong style="font-size: 1.3rem; color: var(--primary-blue);" id="totalCost">₹0</strong>
                        </div>
                        <small style="color: var(--text-light);">+ ₹50 platform fee at checkout</small>
                    </div>

                    <hr style="margin: 2rem 0; border: none; border-top: 2px solid var(--border);">

                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-map-marker-alt"></i> Service Location</h3>

                    <?php if($user_address && !empty($user_address['area'])): ?>
                        <div style="background: #eff6ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 2px solid var(--primary-blue);">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600; color: var(--primary-blue);">
                                <input type="checkbox" name="use_saved_address" value="1" id="useSavedAddress" onchange="toggleAddressForm()">
                                Use my registered address
                            </label>
                            <div style="background: #f0fdf4; padding: 1rem; border-radius: 8px; margin-top: 0.5rem; border-left: 4px solid var(--success); display: none;" id="savedAddressDisplay">
                                <p style="margin: 0; color: #065f46;">
                                    <strong><?php echo htmlspecialchars($user_address['house_number']); ?></strong>, 
                                    <?php echo htmlspecialchars($user_address['street']); ?>,<br>
                                    <?php echo htmlspecialchars($user_address['area']); ?>, 
                                    Hyderabad - <?php echo htmlspecialchars($user_address['pincode']); ?>
                                    <?php if($user_address['landmark']): ?>
                                        <br><small>Near: <?php echo htmlspecialchars($user_address['landmark']); ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div id="newAddressForm">
                        <?php 
                        $address_data = [];
                        include 'components/address-form.php'; 
                        ?>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Special Requests (Optional)</label>
                        <textarea name="special_requests" rows="3" placeholder="Any specific requirements or instructions for the provider..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="proceedBtn" disabled>
                        <i class="fas fa-calendar-check"></i> Proceed to Payment
                    </button>
                </form>
                <?php else: ?>
                <div style="background: #fee2e2; padding: 2rem; border-radius: 8px; text-align: center; border-left: 4px solid var(--danger);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; color: #991b1b; margin-bottom: 1rem;"></i>
                    <p style="color: #991b1b; font-weight: 600; margin-bottom: 0.5rem;">
                        This provider hasn't added any services yet
                    </p>
                    <p style="color: #7f1d1d; font-size: 0.9rem;">
                        Please check back later or contact the provider directly for service availability.
                    </p>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let selectedService = null;

        function updateBookingDetails() {
            const catalogSelect = document.getElementById('catalogSelect');
            const selectedOption = catalogSelect.options[catalogSelect.selectedIndex];
            
            if(selectedOption.value) {
                const price = parseFloat(selectedOption.dataset.price);
                const duration = parseInt(selectedOption.dataset.duration);
                const name = selectedOption.dataset.name;
                const description = selectedOption.dataset.description;
                
                selectedService = {
                    price: price,
                    duration: duration,
                    name: name
                };
                
                // Show service details
                document.getElementById('serviceDetailsBox').style.display = 'block';
                document.getElementById('selectedServiceName').textContent = name;
                document.getElementById('selectedServiceDesc').textContent = description;
                
                // Format duration
                const hours = Math.floor(duration / 60);
                const mins = duration % 60;
                let durationText = '';
                if(hours > 0) durationText += hours + ' hour' + (hours > 1 ? 's' : '');
                if(mins > 0) durationText += (hours > 0 ? ' ' : '') + mins + ' mins';
                
                document.getElementById('selectedDuration').textContent = durationText;
                document.getElementById('selectedPrice').textContent = '₹' + price.toFixed(2);
                document.getElementById('totalCost').textContent = '₹' + price.toFixed(2);
                
                // Enable proceed button
                document.getElementById('proceedBtn').disabled = false;
                
                // Recalculate end time if start time is already selected
                calculateEndTime();
            } else {
                document.getElementById('serviceDetailsBox').style.display = 'none';
                document.getElementById('endTimeDisplay').style.display = 'none';
                document.getElementById('totalCost').textContent = '₹0';
                document.getElementById('proceedBtn').disabled = true;
                selectedService = null;
            }
        }

        function calculateEndTime() {
            const startTimeInput = document.getElementById('startTime');
            const bookingDateInput = document.getElementById('bookingDate');
            
            if(!startTimeInput.value || !bookingDateInput.value || !selectedService) return;
            
            const bookingDate = bookingDateInput.value;
            const startTime = startTimeInput.value;
            const duration = selectedService.duration;
            
            // Create date objects
            const startDateTime = new Date(bookingDate + 'T' + startTime);
            const endDateTime = new Date(startDateTime.getTime() + (duration * 60000)); // Add duration in milliseconds
            
            // Format end time
            const endTimeString = endDateTime.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });
            
            // Display end time
            document.getElementById('calculatedEndTime').textContent = endTimeString;
            document.getElementById('endTimeDisplay').style.display = 'block';
        }

        // Real-time validation for date and time
        document.getElementById('bookingDate')?.addEventListener('change', validateDateTime);
        document.getElementById('startTime')?.addEventListener('change', function() {
            validateDateTime();
            calculateEndTime();
        });

        function validateDateTime() {
            const bookingDate = document.getElementById('bookingDate').value;
            const startTime = document.getElementById('startTime').value;

            if(!bookingDate || !startTime) return;

            const now = new Date();
            const selectedDateTime = new Date(bookingDate + 'T' + startTime);

            // Check if selected date/time is in the past
            if(selectedDateTime <= now) {
                alert('You cannot book for past dates or times. Please select a future time.');
                document.getElementById('startTime').value = '';
                document.getElementById('endTimeDisplay').style.display = 'none';
                return;
            }
        }

        // Set minimum time if today is selected
        document.getElementById('bookingDate')?.addEventListener('change', function() {
            const selectedDate = this.value;
            const today = new Date().toISOString().split('T')[0];
            const startTimeInput = document.getElementById('startTime');

            if(selectedDate === today) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const currentTime = `${hours}:${minutes}`;
                startTimeInput.setAttribute('min', currentTime);
            } else {
                startTimeInput.removeAttribute('min');
            }
            
            // Recalculate end time when date changes
            calculateEndTime();
        });

        function toggleAddressForm() {
            const checkbox = document.getElementById('useSavedAddress');
            const newAddressForm = document.getElementById('newAddressForm');
            const savedAddressDisplay = document.getElementById('savedAddressDisplay');
            
            if(checkbox.checked) {
                newAddressForm.style.display = 'none';
                savedAddressDisplay.style.display = 'block';
                newAddressForm.querySelectorAll('input[required], select[required]').forEach(input => {
                    input.removeAttribute('required');
                });
            } else {
                newAddressForm.style.display = 'block';
                savedAddressDisplay.style.display = 'none';
                document.querySelector('input[name="house_number"]')?.setAttribute('required', 'required');
                document.querySelector('input[name="street"]')?.setAttribute('required', 'required');
                document.querySelector('select[name="area"]')?.setAttribute('required', 'required');
                document.querySelector('input[name="pincode"]')?.setAttribute('required', 'required');
            }
        }
    </script>
</body>
</html>
