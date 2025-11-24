<?php 
require_once 'config.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$user_id = $_SESSION['user_id'];

if($booking_id == 0) {
    header("Location: my-bookings.php");
    exit();
}

// Get booking details
$sql = "SELECT b.*, sp.provider_name, sp.provider_id, sc.category_name 
        FROM bookings b 
        JOIN service_providers sp ON b.provider_id = sp.provider_id 
        JOIN service_categories sc ON b.category_id = sc.category_id 
        WHERE b.booking_id = $booking_id AND b.user_id = $user_id AND b.booking_status = 'completed'";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: my-bookings.php");
    exit();
}

$booking = $result->fetch_assoc();

// Check if already reviewed
$check_review = $conn->query("SELECT * FROM reviews WHERE booking_id = $booking_id AND user_id = $user_id");
if($check_review->num_rows > 0) {
    $_SESSION['error_message'] = "You have already reviewed this service.";
    header("Location: my-bookings.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $review_text = sanitize($_POST['review_text']);
    $provider_id = $booking['provider_id'];
    
    if($rating < 1 || $rating > 5) {
        $error = "Please select a rating between 1 and 5 stars";
    } elseif(empty($review_text)) {
        $error = "Please write a review";
    } else {
        // Insert review
        $insert_sql = "INSERT INTO reviews (booking_id, user_id, provider_id, rating, review_text) 
                      VALUES ($booking_id, $user_id, $provider_id, $rating, '$review_text')";
        
        if($conn->query($insert_sql)) {
            // Update provider's average rating
            $update_rating = $conn->query("UPDATE service_providers SET 
                rating = (SELECT AVG(rating) FROM reviews WHERE provider_id = $provider_id) 
                WHERE provider_id = $provider_id");
            
            $_SESSION['success_message'] = "Thank you for your review!";
            header("Location: my-bookings.php");
            exit();
        } else {
            $error = "Failed to submit review. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Review - Intzi</title>
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
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 5%;
        }

        .review-container {
            background: var(--white);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .booking-info {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
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

        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 150px;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .rating-input {
            display: flex;
            gap: 0.5rem;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input label {
            cursor: pointer;
            color: #ddd;
            transition: all 0.3s;
        }

        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label {
            color: var(--warning);
        }

        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
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

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }
            .review-container {
                padding: 2rem;
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

    <div class="container">
        <div class="review-container">
            <h1 style="text-align: center; margin-bottom: 2rem;">Write a Review</h1>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="booking-info">
                <h3 style="margin-bottom: 1rem;">Service Details</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <strong>Provider:</strong><br>
                        <?php echo htmlspecialchars($booking['provider_name']); ?>
                    </div>
                    <div>
                        <strong>Service:</strong><br>
                        <?php echo htmlspecialchars($booking['category_name']); ?>
                    </div>
                    <div>
                        <strong>Date:</strong><br>
                        <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label>How would you rate this service? *</label>
                    <div class="rating-input">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1"><i class="fas fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tell us about your experience *</label>
                    <textarea name="review_text" required placeholder="Share your experience with this service provider. What did you like? What could be improved?"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="my-bookings.php" style="color: var(--primary-blue); font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back to My Bookings
                </a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Intzi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Add hover effect for star ratings
        const stars = document.querySelectorAll('.rating-input label');
        
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                highlightStars(index);
            });
        });

        document.querySelector('.rating-input').addEventListener('mouseleave', () => {
            const checked = document.querySelector('.rating-input input:checked');
            if (checked) {
                const checkedIndex = Array.from(stars).indexOf(checked.nextElementSibling);
                highlightStars(checkedIndex);
            } else {
                resetStars();
            }
        });

        function highlightStars(index) {
            stars.forEach((star, i) => {
                if (i <= index) {
                    star.style.color = '#f59e0b';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }

        function resetStars() {
            stars.forEach(star => {
                star.style.color = '#ddd';
            });
        }
    </script>
</body>
</html>
