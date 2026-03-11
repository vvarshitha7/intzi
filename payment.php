<?php 
require_once 'config.php';
requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if($booking_id == 0) {
    header("Location: services.php");
    exit();
}

// Get booking details with provider info
$sql = "SELECT b.*, 
        sp.provider_name, sp.min_price, sp.max_price, sp.profile_image,
        sc.category_name,
        u.full_name, u.email, u.phone,
        pc.service_name as catalog_service_name, pc.price as catalog_price
        FROM bookings b 
        JOIN service_providers sp ON b.provider_id = sp.provider_id
        JOIN service_categories sc ON b.category_id = sc.category_id
        JOIN users u ON b.user_id = u.user_id 
        LEFT JOIN provider_catalog pc ON b.catalog_item_id = pc.catalog_id
        WHERE b.booking_id = $booking_id AND b.user_id = {$_SESSION['user_id']}";

$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: services.php");
    exit();
}

$booking = $result->fetch_assoc();

// Calculate service amount (total_amount includes platform fee already)
$total_amount = $booking['total_amount'];
$platform_fee = 50;
$service_amount = $total_amount - $platform_fee;

// Calculate duration in hours for display
$start = new DateTime($booking['start_time']);
$end = new DateTime($booking['end_time']);
$duration = $start->diff($end);
$duration_hours = $duration->h + ($duration->i / 60);
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
            max-width: 1000px;
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

        .btn-pay:disabled {
            background: var(--text-light);
            cursor: not-allowed;
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

        .method-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .method-tab {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }

        .method-tab i {
            font-size: 1.5rem;
        }

        .method-tab.active {
            border-color: var(--primary-blue);
            background: #eff6ff;
            color: var(--primary-blue);
        }

        .method-content {
            display: none;
        }

        .method-content.active {
            display: block;
        }

        .bank-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .bank-option:hover {
            border-color: var(--primary-blue);
            background: #eff6ff;
        }

        .bank-option input[type="radio"] {
            margin-right: 1rem;
            width: 20px;
            height: 20px;
        }

        .bank-logo {
            width: 50px;
            height: 50px;
            margin-right: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .netbanking-step {
            background: #eff6ff;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid var(--primary-blue);
        }

        .netbanking-step h4 {
            margin-bottom: 1rem;
            color: var(--primary-blue);
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

        .error-message {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: none;
        }

        .upi-note {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .upi-apps {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .upi-app {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--text-dark);
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
            <p>Complete your payment to confirm booking #<?php echo $booking_id; ?></p>
        </div>

        <div class="payment-content">
            <div class="payment-form">
                <h3 style="margin-bottom: 1rem;">Select Payment Method</h3>
                <div class="method-tabs">
                    <div class="method-tab active" onclick="switchMethod('card')" data-method="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Card</span>
                    </div>
                    <div class="method-tab" onclick="switchMethod('upi')" data-method="upi">
                        <i class="fas fa-mobile-alt"></i>
                        <span>UPI</span>
                    </div>
                    <div class="method-tab" onclick="switchMethod('netbanking')" data-method="netbanking">
                        <i class="fas fa-university"></i>
                        <span>Net Banking</span>
                    </div>
                </div>

                <!-- Card Payment Form -->
                <form id="paymentForm" method="POST" action="process-payment.php">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
                    <input type="hidden" name="payment_method" id="paymentMethod" value="card">
                    
                    <!-- Card Method -->
                    <div id="card-method" class="method-content active">
                        <div class="form-group">
                            <label>Card Number *</label>
                            <input type="text" name="card_number" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" pattern="[0-9\s]{19}">
                            <div class="card-icons">
                                <div class="card-icon" style="color: #1434CB;">VISA</div>
                                <div class="card-icon" style="color: #EB001B;">MC</div>
                                <div class="card-icon" style="color: #006FCF;">AMEX</div>
                                <div class="card-icon" style="color: #00A3E0;">RUPAY</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Cardholder Name *</label>
                            <input type="text" name="card_name" placeholder="JOHN DOE" pattern="[A-Za-z\s]+">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date (MM/YY) *</label>
                                <input type="text" name="expiry" id="expiryDate" placeholder="MM/YY" maxlength="5">
                                <span class="error-message" id="expiryError">Invalid expiry date</span>
                            </div>
                            <div class="form-group">
                                <label>CVV *</label>
                                <input type="password" name="cvv" id="cvv" placeholder="123" maxlength="4" pattern="[0-9]{3,4}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pay" id="cardPayBtn">
                            <i class="fas fa-lock"></i>
                            Pay ₹<?php echo number_format($total_amount, 2); ?>
                        </button>
                    </div>

                    <!-- UPI Method -->
                    <div id="upi-method" class="method-content">
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <i class="fas fa-mobile-alt" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                            <h4 style="color: var(--text-dark);">Pay using UPI</h4>
                        </div>

                        <div class="upi-apps">
                            <div class="upi-app">
                                <i class="fab fa-google-pay" style="color: #4285F4;"></i> Google Pay
                            </div>
                            <div class="upi-app">
                                <i class="fas fa-mobile-alt" style="color: #5f259f;"></i> PhonePe
                            </div>
                            <div class="upi-app">
                                <i class="fas fa-wallet" style="color: #00BAF2;"></i> Paytm
                            </div>
                            <div class="upi-app">
                                <i class="fas fa-university" style="color: #097AFF;"></i> BHIM
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Enter Your UPI ID *</label>
                            <input type="text" name="upi_id" id="upiId" placeholder="yourname@paytm">
                            <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">
                                <i class="fas fa-info-circle"></i> Example: 9876543210@paytm, username@oksbi, mobile@ybl
                            </small>
                        </div>

                        <div class="upi-note">
                            <strong><i class="fas fa-info-circle"></i> Demo Mode:</strong><br>
                            Enter any UPI ID and click confirm. In production, this would integrate with actual payment gateways like Razorpay or PhonePe.
                        </div>

                        <button type="submit" class="btn btn-pay">
                            <i class="fas fa-check-circle"></i>
                            Confirm UPI Payment (Demo)
                        </button>
                    </div>

                    <!-- Net Banking Method -->
                    <div id="netbanking-method" class="method-content">
                        <h4 style="margin-bottom: 1rem;">Select Your Bank</h4>
                        
                        <label class="bank-option">
                            <input type="radio" name="bank" value="SBI">
                            <div class="bank-logo">SBI</div>
                            <div>
                                <strong>State Bank of India</strong><br>
                                <small style="color: var(--text-light);">India's largest bank</small>
                            </div>
                        </label>

                        <label class="bank-option">
                            <input type="radio" name="bank" value="HDFC">
                            <div class="bank-logo">HDFC</div>
                            <div>
                                <strong>HDFC Bank</strong><br>
                                <small style="color: var(--text-light);">Private sector bank</small>
                            </div>
                        </label>

                        <label class="bank-option">
                            <input type="radio" name="bank" value="ICICI">
                            <div class="bank-logo">ICICI</div>
                            <div>
                                <strong>ICICI Bank</strong><br>
                                <small style="color: var(--text-light);">Leading private bank</small>
                            </div>
                        </label>

                        <label class="bank-option">
                            <input type="radio" name="bank" value="AXIS">
                            <div class="bank-logo">AXIS</div>
                            <div>
                                <strong>Axis Bank</strong><br>
                                <small style="color: var(--text-light);">Third largest private bank</small>
                            </div>
                        </label>

                        <label class="bank-option">
                            <input type="radio" name="bank" value="PNB">
                            <div class="bank-logo">PNB</div>
                            <div>
                                <strong>Punjab National Bank</strong><br>
                                <small style="color: var(--text-light);">Public sector bank</small>
                            </div>
                        </label>

                        <div class="netbanking-step">
                            <h4><i class="fas fa-info-circle"></i> Demo Mode</h4>
                            <p>Select your bank and click the button below to simulate Net Banking payment. In production, you would be redirected to your bank's secure login page.</p>
                        </div>

                        <button type="submit" class="btn btn-pay" style="margin-top: 1rem;">
                            <i class="fas fa-check-circle"></i>
                            Confirm Bank Payment (Demo)
                        </button>
                    </div>
                </form>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your payment information is secure and encrypted</span>
                </div>
            </div>

            <div class="payment-summary">
                <h3 style="margin-bottom: 1.5rem;">Booking Summary</h3>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Booking ID</span>
                    <strong>#<?php echo $booking_id; ?></strong>
                </div>

                <div class="summary-item">
                    <span style="color: var(--text-light);">Service Provider</span>
                    <strong><?php echo htmlspecialchars($booking['provider_name']); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Service Category</span>
                    <strong><?php echo htmlspecialchars($booking['category_name']); ?></strong>
                </div>

                <?php if($booking['catalog_service_name']): ?>
                <div class="summary-item">
                    <span style="color: var(--text-light);">Selected Service</span>
                    <strong><?php echo htmlspecialchars($booking['catalog_service_name']); ?></strong>
                </div>
                <?php endif; ?>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Date</span>
                    <strong><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Time Slot</span>
                    <strong>
                        <?php echo date('h:i A', strtotime($booking['start_time'])); ?> - 
                        <?php echo date('h:i A', strtotime($booking['end_time'])); ?>
                    </strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Duration</span>
                    <strong><?php echo number_format($duration_hours, 1); ?> hours</strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Service Charge</span>
                    <strong>₹<?php echo number_format($service_amount, 2); ?></strong>
                </div>
                
                <div class="summary-item">
                    <span style="color: var(--text-light);">Platform Fee</span>
                    <strong>₹<?php echo number_format($platform_fee, 2); ?></strong>
                </div>
                
                <div class="summary-item" style="border: none;">
                    <div class="summary-total">
                        <div>Total Amount</div>
                        <div>₹<?php echo number_format($total_amount, 2); ?></div>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <p style="font-size: 0.85rem; color: var(--text-light); line-height: 1.6;">
                        <strong>Note:</strong> This is a demo payment gateway. All payments are simulated for demonstration purposes.
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

        // Auto-format expiry date with validation
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length >= 2) {
                let month = value.substring(0, 2);
                let year = value.substring(2, 4);
                
                // Validate month (01-12)
                if (parseInt(month) > 12) {
                    month = '12';
                }
                if (parseInt(month) === 0) {
                    month = '01';
                }
                
                e.target.value = month + (year ? '/' + year : '');
            } else {
                e.target.value = value;
            }
            
            validateExpiry();
        });

        function validateExpiry() {
            const expiryInput = document.getElementById('expiryDate');
            const expiryError = document.getElementById('expiryError');
            const value = expiryInput.value;
            
            if (value.length === 5) {
                const [month, year] = value.split('/');
                const currentYear = new Date().getFullYear() % 100;
                const currentMonth = new Date().getMonth() + 1;
                
                if (parseInt(year) < currentYear || 
                    (parseInt(year) === currentYear && parseInt(month) < currentMonth)) {
                    expiryError.style.display = 'block';
                    expiryInput.style.borderColor = 'var(--danger)';
                    return false;
                } else {
                    expiryError.style.display = 'none';
                    expiryInput.style.borderColor = 'var(--success)';
                    return true;
                }
            }
            return false;
        }

        // Switch payment method
        function switchMethod(method) {
            // Update tabs
            document.querySelectorAll('.method-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.closest('.method-tab').classList.add('active');
            
            // Update content
            document.querySelectorAll('.method-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(method + '-method').classList.add('active');
            
            // Update payment method value
            document.getElementById('paymentMethod').value = method;
            
            // Update required fields
            updateRequiredFields(method);
        }

        function updateRequiredFields(method) {
            // Remove all required attributes first
            document.querySelectorAll('#card-method input').forEach(input => {
                input.removeAttribute('required');
            });
            document.querySelectorAll('#upi-method input').forEach(input => {
                input.removeAttribute('required');
            });
            document.querySelectorAll('#netbanking-method input').forEach(input => {
                input.removeAttribute('required');
            });
            
            // Add required to active method
            if(method === 'card') {
                document.getElementById('cardNumber').setAttribute('required', 'required');
                document.querySelector('input[name="card_name"]').setAttribute('required', 'required');
                document.getElementById('expiryDate').setAttribute('required', 'required');
                document.getElementById('cvv').setAttribute('required', 'required');
            } else if(method === 'upi') {
                document.getElementById('upiId').setAttribute('required', 'required');
            } else if(method === 'netbanking') {
                const firstRadio = document.querySelector('input[name="bank"]');
                if(firstRadio) firstRadio.setAttribute('required', 'required');
            }
        }

        // Initialize required fields for card (default method)
        updateRequiredFields('card');

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const method = document.getElementById('paymentMethod').value;
            
            // Validate based on method
            if(method === 'card') {
                if(!validateExpiry()) {
                    alert('Please enter a valid expiry date');
                    return false;
                }
            }
            
            if(method === 'upi') {
                const upiId = document.getElementById('upiId').value.trim();
                if(!upiId || !upiId.includes('@')) {
                    alert('Please enter a valid UPI ID (e.g., name@paytm)');
                    return false;
                }
            }
            
            if(method === 'netbanking') {
                const selectedBank = document.querySelector('input[name="bank"]:checked');
                if(!selectedBank) {
                    alert('Please select a bank');
                    return false;
                }
            }
            
            // Show processing overlay
            document.getElementById('processingOverlay').style.display = 'flex';
            
            // Submit after 2 seconds
            setTimeout(() => {
                this.submit();
            }, 2000);
        });
    </script>
</body>
</html>
