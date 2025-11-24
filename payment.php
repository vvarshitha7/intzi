<?php 
require_once 'config.php';
require_once 'payment-config.php';
requireLogin();

if(!isset($_SESSION['booking_id']) || !isset($_SESSION['payment_amount'])) {
    header("Location: services.php");
    exit();
}

$booking_id = $_SESSION['booking_id'];
$amount = $_SESSION['payment_amount'];
$provider_name = $_SESSION['provider_name'];
$category_name = $_SESSION['category_name'];

// Get booking details
$sql = "SELECT b.*, u.full_name, u.email, u.phone 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        WHERE b.booking_id = $booking_id";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - Intzi</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .payment-container {
            max-width: 900px;
            width: 100%;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .payment-header p {
            opacity: 0.9;
        }

        .payment-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .payment-form {
            padding: 2rem;
        }

        .payment-summary {
            background: var(--bg-light);
            padding: 2rem;
            border-left: 1px solid var(--border);
        }

        .form-group {
            margin-bottom: 1.5rem;
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
            padding: 0.9rem 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .card-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .card-icon {
            width: 40px;
            height: 25px;
            border: 1px solid var(--border);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-pay {
            background: var(--primary-blue);
            color: var(--white);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-pay:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            padding-top: 1rem;
        }

        .secure-badge {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #d1fae5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #065f46;
            font-size: 0.9rem;
        }

        .payment-methods {
            margin-top: 1.5rem;
        }

        .payment-methods h4 {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .method-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .method-tab {
            padding: 0.8rem 1.2rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .method-tab.active {
            border-color: var(--primary-blue);
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .method-content {
            display: none;
        }

        .method-content.active {
            display: block;
        }

        #processingOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .processing-content {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
        }

        .spinner {
            border: 4px solid var(--border);
            border-top: 4px solid var(--primary-blue);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .payment-content {
                grid-template-columns: 1fr;
            }
            .payment-summary {
                border-left: none;
                border-top: 1px solid var(--border);
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="fas fa-shield-alt"></i> Secure Payment Gateway</h1>
            <p>Complete your payment to confirm booking</p>
        </div>

        <div class="payment-content">
            <div class="payment-form">
                <div class="payment-methods">
                    <h4>Select Payment Method</h4>
                    <div class="method-tabs">
                        <div class="method-tab active" onclick="switchMethod('card')">
                            <i class="fas fa-credit-card"></i> Card
                        </div>
                        <div class="method-tab" onclick="switchMethod('upi')">
                            <i class="fas fa-mobile-alt"></i> UPI
                        </div>
                        <div class="method-tab" onclick="switchMethod('netbanking')">
                            <i class="fas fa-university"></i> Net Banking
                        </div>
                    </div>
                </div>

                <!-- Card Payment Form -->
                <form id="paymentForm" onsubmit="processPayment(event)" method="POST" action="process-payment.php">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    
                    <!-- Card Method -->
                    <div id="card-method" class="method-content active">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                            <div class="card-icons">
                                <div class="card-icon" style="color: #1434CB;">VISA</div>
                                <div class="card-icon" style="color: #EB001B;">MC</div>
                                <div class="card-icon" style="color: #006FCF;">AMEX</div>
                                <div class="card-icon" style="color: #00A3E0;">RUPAY</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Cardholder Name</label>
                            <input type="text" name="card_name" placeholder="John Doe" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="password" name="cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                    </div>

                    <!-- UPI Method -->
                    <div id="upi-method" class="method-content">
                        <div class="form-group">
                            <label>UPI ID</label>
                            <input type="text" name="upi_id" placeholder="yourname@upi">
                        </div>
                        <p style="color: var(--text-light); font-size: 0.85rem; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Enter your UPI ID (Google Pay, PhonePe, Paytm, etc.)
                        </p>
                    </div>

                    <!-- Net Banking Method -->
                    <div id="netbanking-method" class="method-content">
                        <div class="form-group">
                            <label>Select Your Bank</label>
                            <select name="bank">
                                <option value="">Choose Bank</option>
                                <option value="SBI">State Bank of India</option>
                                <option value="HDFC">HDFC Bank</option>
                                <option value="ICICI">ICICI Bank</option>
                                <option value="AXIS">Axis Bank</option>
                                <option value="PNB">Punjab National Bank</option>
                                <option value="BOB">Bank of Baroda</option>
                                <option value="KOTAK">Kotak Mahindra Bank</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method" id="paymentMethod" value="Credit Card">

                    <button type="submit" class="btn btn-pay">
                        <i class="fas fa-lock"></i>
                        Pay ₹<?php echo number_format($amount, 2); ?>
                    </button>
                </form>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your payment information is secure and encrypted</span>
                </div>
            </div>

            <div class="payment-summary">
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Service Provider</span>
                    <strong><?php echo htmlspecialchars($provider_name); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Service</span>
                    <strong><?php echo htmlspecialchars($category_name); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Date & Time</span>
                    <strong><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br><?php echo date('h:i A', strtotime($booking['booking_time'])); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Duration</span>
                    <strong><?php echo $booking['duration_hours']; ?> hours</strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Service Charge</span>
                    <strong>₹<?php echo number_format($amount - 50, 2); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Platform Fee</span>
                    <strong>₹50.00</strong>
                </div>
                
                <div class="summary-item" style="border: none;">
                    <div class="summary-total">
                        <div>Total Amount</div>
                        <div>₹<?php echo number_format($amount, 2); ?></div>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <p style="font-size: 0.85rem; color: var(--text-light); line-height: 1.6;">
                        <strong>Note:</strong> This is a demo payment gateway for demonstration purposes. No real money will be charged.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div id="processingOverlay">
        <div class="processing-content">
            <div class="spinner"></div>
            <h3>Processing Payment...</h3>
            <p style="color: var(--text-light); margin-top: 0.5rem;">Please wait while we process your transaction</p>
        </div>
    </div>

    <script>
        // Format card number with spaces
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Switch payment method
        function switchMethod(method) {
            // Update tabs
            document.querySelectorAll('.method-tab').forEach(tab => tab.classList.remove('active'));
            event.target.closest('.method-tab').classList.add('active');
            
            // Update content
            document.querySelectorAll('.method-content').forEach(content => content.classList.remove('active'));
            document.getElementById(method + '-method').classList.add('active');
            
            // Update payment method value
            let methodName = method === 'card' ? 'Credit Card' : method === 'upi' ? 'UPI' : 'Net Banking';
            document.getElementById('paymentMethod').value = methodName;
        }

        // Process payment
        function processPayment(e) {
            e.preventDefault();
            
            // Show processing overlay
            document.getElementById('processingOverlay').style.display = 'flex';
            
            // Simulate payment processing (2 seconds delay)
            setTimeout(function() {
                // Submit form
                e.target.submit();
            }, 2000);
        }
    </script>
</body>
</html>
