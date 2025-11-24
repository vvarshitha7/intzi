<?php 
require_once 'config.php';
require_once 'payment-config.php';
requireLogin();

$provider_id = isset($_GET['provider']) ? (int)$_GET['provider'] : 0;

if($provider_id == 0) {
    header("Location: services.php");
    exit();
}

$sql = "SELECT sp.*, sc.category_name 
        FROM service_providers sp 
        JOIN service_categories sc ON sp.category_id = sc.category_id 
        WHERE sp.provider_id = $provider_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: services.php");
    exit();
}

$provider = $result->fetch_assoc();
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_booking'])) {
    $booking_type = sanitize($_POST['booking_type']);
    $booking_date = sanitize($_POST['booking_date']);
    $booking_time = sanitize($_POST['booking_time']);
    $duration = (float)$_POST['duration'];
    $address = sanitize($_POST['address']);
    $special_requirements = sanitize($_POST['special_requirements']);
    
    $service_fee = PAYMENT_SERVICE_FEE;
    $subtotal = $provider['hourly_rate'] * $duration;
    $total_amount = $subtotal + $service_fee;
    
    $user_id = $_SESSION['user_id'];
    $category_id = $provider['category_id'];
    
    // Create booking
    $sql = "INSERT INTO bookings (user_id, provider_id, category_id, booking_type, booking_date, booking_time, duration_hours, total_amount, address, special_requirements, booking_status, payment_status) 
            VALUES ($user_id, $provider_id, $category_id, '$booking_type', '$booking_date', '$booking_time', $duration, $total_amount, '$address', '$special_requirements', 'pending', 'pending')";
    
    if($conn->query($sql)) {
        $booking_id = $conn->insert_id;
        
        // Store in session for payment page
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['payment_amount'] = $total_amount;
        $_SESSION['provider_name'] = $provider['provider_name'];
        $_SESSION['category_name'] = $provider['category_name'];
        
        // Redirect to payment page
        header("Location: payment.php");
        exit();
    } else {
        $error = "Booking failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --light-blue: #dbeafe;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --success: #10b981;
            --border: #e5e7eb;
            --danger: #ef4444;
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

        .btn-outline {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .btn-full {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .section {
            padding: 3rem 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .booking-summary {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-blue);
            padding-top: 1rem;
        }

        .footer {
            background: #1e3a8a;
            color: var(--white);
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .footer-bottom {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 968px) {
            .booking-grid {
                grid-template-columns: 1fr !important;
            }
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
                <li><a href="my-bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="section">
        <div class="container">
            <h1 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem;">Complete Your Booking</h1>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="booking-grid" style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem;">
                <div style="background: white; padding: 2rem; border-radius: 16px;">
                    <h2 style="margin-bottom: 1.5rem;">Booking Details</h2>
                    
                    <form method="POST" action="" id="bookingForm">
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Booking Type *</label>
                            <select name="booking_type" required>
                                <option value="instant">Instant (Today)</option>
                                <option value="scheduled">Schedule for Later</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Date *</label>
                            <input type="date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Time *</label>
                            <input type="time" name="booking_time" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-hourglass-half"></i> Duration (hours) *</label>
                            <input type="number" name="duration" min="1" max="12" step="0.5" value="1" required id="duration" onchange="calculateTotal()">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Service Address *</label>
                            <textarea name="address" required placeholder="Enter the full address where service is needed"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Special Requirements</label>
                            <textarea name="special_requirements" placeholder="Any special instructions or requirements"></textarea>
                        </div>
                        
                        <button type="submit" name="create_booking" class="btn btn-primary btn-full">
                            <i class="fas fa-lock"></i> Proceed to Payment
                        </button>
                    </form>
                </div>
                
                <div>
                    <div class="booking-summary">
                        <h3 style="margin-bottom: 1.5rem;">Booking Summary</h3>
                        
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <img src="images/providers/<?php echo htmlspecialchars($provider['profile_image']); ?>" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 1rem;"
                                 onerror="this.src='images/default-provider.jpg'">
                            <h4><?php echo htmlspecialchars($provider['provider_name']); ?></h4>
                            <p style="color: var(--text-light);"><?php echo htmlspecialchars($provider['category_name']); ?></p>
                        </div>
                        
                        <div class="summary-item">
                            <span>Hourly Rate</span>
                            <strong>₹<?php echo number_format($provider['hourly_rate'], 2); ?></strong>
                        </div>
                        
                        <div class="summary-item">
                            <span>Duration</span>
                            <strong id="displayDuration">1 hour</strong>
                        </div>
                        
                        <div class="summary-item">
                            <span>Subtotal</span>
                            <strong id="subtotal">₹<?php echo number_format($provider['hourly_rate'], 2); ?></strong>
                        </div>
                        
                        <div class="summary-item">
                            <span>Service Fee</span>
                            <strong>₹50.00</strong>
                        </div>
                        
                        <div class="summary-item" style="border: none;">
                            <div class="summary-total">
                                <div>Total Amount</div>
                                <div id="totalAmount">₹<?php echo number_format($provider['hourly_rate'] + 50, 2); ?></div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; color: var(--text-light); font-size: 0.9rem;">
                                <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                                <span>Secure Payment Gateway</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const hourlyRate = <?php echo $provider['hourly_rate']; ?>;
        const serviceFee = 50;
        
        function calculateTotal() {
            const duration = parseFloat(document.getElementById('duration').value) || 1;
            const subtotal = hourlyRate * duration;
            const total = subtotal + serviceFee;
            
            document.getElementById('displayDuration').textContent = duration + (duration === 1 ? ' hour' : ' hours');
            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
        }
        
        document.getElementById('duration').addEventListener('input', calculateTotal);
    </script>
</body>
</html>
