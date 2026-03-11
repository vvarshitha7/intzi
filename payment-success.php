<?php 
require_once 'config.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease;
        }
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        h1 { color: #10b981; margin-bottom: 1rem; }
        p { color: #6b7280; margin-bottom: 0.5rem; }
        .transaction-id {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            margin: 2rem 0;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: #2563eb;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your booking has been confirmed</p>
        <p><strong>Booking ID:</strong> #<?php echo $booking_id; ?></p>
        <div class="transaction-id">
            <strong>Transaction ID:</strong><br>
            <?php echo $transaction_id; ?>
        </div>
        <p>You will receive a confirmation email shortly</p>
        <a href="my-bookings.php" class="btn">
            <i class="fas fa-calendar-check"></i> View My Bookings
        </a>
    </div>
</body>
</html>
