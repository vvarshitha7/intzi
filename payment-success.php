<?php 
require_once 'config.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$transaction_id = isset($_SESSION['transaction_id']) ? $_SESSION['transaction_id'] : 'N/A';

if($booking_id > 0) {
    $sql = "SELECT b.*, sp.provider_name, sc.category_name 
            FROM bookings b 
            JOIN service_providers sp ON b.provider_id = sp.provider_id 
            JOIN service_categories sc ON b.category_id = sc.category_id 
            WHERE b.booking_id = $booking_id AND b.user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        header("Location: my-bookings.php");
        exit();
    }
} else {
    header("Location: my-bookings.php");
    exit();
}

unset($_SESSION['payment_success']);
unset($_SESSION['transaction_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Intzi</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .success-container {
            max-width: 600px;
            width: 100%;
            background: var(--white);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--success);
            margin-bottom: 1.5rem;
            animation: scaleIn 0.5s ease-in-out;
        }

        @keyframes scaleIn {
            0% { transform: scale(0) rotate(0deg); }
            50% { transform: scale(1.1) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .booking-details {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.7rem 0;
            border-bottom: 1px solid var(--border);
        }

        .detail-row:last-child {
            border: none;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            display: inline-block;
            margin: 0.5rem;
            text-decoration: none;
            transition: all 0.3s;
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

        .btn:hover {
            transform: translateY(-2px);
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--primary-blue);
            position: absolute;
            animation: confetti-fall 3s linear;
        }

        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Payment Successful!</h1>
        <p style="color: var(--text-light); margin-bottom: 2rem;">Your booking has been confirmed successfully</p>
        
        <div class="booking-details">
            <div class="detail-row">
                <span style="color: var(--text-light);">Booking ID</span>
                <strong>#<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Transaction ID</span>
                <strong style="font-size: 0.85rem;"><?php echo htmlspecialchars($transaction_id); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Service Provider</span>
                <strong><?php echo htmlspecialchars($booking['provider_name']); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Service</span>
                <strong><?php echo htmlspecialchars($booking['category_name']); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Date & Time</span>
                <strong><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?> at <?php echo date('h:i A', strtotime($booking['booking_time'])); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Amount Paid</span>
                <strong style="color: var(--success); font-size: 1.2rem;">₹<?php echo number_format($booking['total_amount'], 2); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Payment Method</span>
                <strong><?php echo htmlspecialchars($booking['payment_method']); ?></strong>
            </div>
            <div class="detail-row">
                <span style="color: var(--text-light);">Status</span>
                <strong style="color: var(--success);">✓ Confirmed</strong>
            </div>
        </div>
        
        <div style="background: #d1fae5; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; color: #065f46;">
            <i class="fas fa-info-circle"></i> A confirmation email has been sent to your registered email address
        </div>
        
        <a href="my-bookings.php" class="btn btn-primary">View My Bookings</a>
        <a href="index.php" class="btn btn-outline">Back to Home</a>
    </div>

    <script>
        // Create confetti effect
        function createConfetti() {
            for(let i = 0; i < 50; i++) {
                let confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = ['#2563eb', '#10b981', '#f59e0b', '#ef4444'][Math.floor(Math.random() * 4)];
                confetti.style.animationDelay = Math.random() * 3 + 's';
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 3000);
            }
        }
        
        createConfetti();
    </script>
</body>
</html>
