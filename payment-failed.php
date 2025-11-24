<?php 
require_once 'config.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$error_message = isset($_SESSION['payment_error']) ? $_SESSION['payment_error'] : 'Payment could not be processed';
unset($_SESSION['payment_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Intzi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .failed-container {
            max-width: 600px;
            width: 100%;
            background: var(--white);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .failed-icon {
            font-size: 5rem;
            color: var(--danger);
            margin-bottom: 1.5rem;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
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
            border: 2px solid var(--text-light);
            color: var(--text-dark);
            background: transparent;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="failed-container">
        <div class="failed-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        
        <h1>Payment Failed</h1>
        <p style="color: var(--text-light); margin-bottom: 2rem;"><?php echo htmlspecialchars($error_message); ?></p>
        
        <div style="background: #fee2e2; padding: 1.5rem; border-radius: 12px; margin: 2rem 0; color: #991b1b; text-align: left;">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-exclamation-triangle"></i> What went wrong?</h3>
            <p style="margin-bottom: 0.5rem;">The payment transaction could not be completed. Your booking is still pending payment.</p>
        </div>
        
        <p style="color: var(--text-light); margin-bottom: 1.5rem;">
            <strong>Common reasons for payment failure:</strong>
        </p>
        <ul style="text-align: left; max-width: 400px; margin: 0 auto 2rem; color: var(--text-light);">
            <li style="margin-bottom: 0.5rem;">Insufficient account balance</li>
            <li style="margin-bottom: 0.5rem;">Incorrect card details or CVV</li>
            <li style="margin-bottom: 0.5rem;">Card expired or blocked</li>
            <li style="margin-bottom: 0.5rem;">Daily transaction limit exceeded</li>
            <li style="margin-bottom: 0.5rem;">Network connection issues</li>
        </ul>
        
        <?php if($booking_id > 0): ?>
            <a href="payment.php" class="btn btn-primary"><i class="fas fa-redo"></i> Retry Payment</a>
        <?php endif; ?>
        <a href="my-bookings.php" class="btn btn-outline">View Bookings</a>
        <a href="index.php" class="btn btn-outline">Back to Home</a>
        
        <p style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-light);">
            Need help? Contact support at <strong>support@intzi.com</strong>
        </p>
    </div>
</body>
</html>
