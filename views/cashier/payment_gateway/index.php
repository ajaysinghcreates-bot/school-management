<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .payment-card { max-width: 500px; margin: 2rem auto; }
        .payment-method { cursor: pointer; transition: all 0.3s; }
        .payment-method:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .payment-method.selected { border-color: #007bff; background-color: #f8f9ff; }
        .secure-badge { color: #28a745; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card payment-card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card"></i> Secure Payment Portal
                        </h4>
                        <small class="text-white-50">School Management System</small>
                    </div>
                    <div class="card-body">
                        <!-- Payment Details -->
                        <div class="mb-4">
                            <h5 class="text-center mb-3">Payment Details</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Student:</strong><br>
                                    <?php echo htmlspecialchars($session['student_name']); ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Email:</strong><br>
                                    <?php echo htmlspecialchars($session['student_email']); ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <strong>Description:</strong><br>
                                    <?php echo htmlspecialchars($session['description']); ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Amount:</strong><br>
                                    <span class="h5 text-success">$<?php echo number_format($session['amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Payment Methods -->
                        <h5 class="text-center mb-3">Select Payment Method</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card payment-method border" data-method="card" onclick="selectPaymentMethod('card')">
                                    <div class="card-body text-center">
                                        <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                        <h6>Credit/Debit Card</h6>
                                        <small class="text-muted">Visa, MasterCard, Amex</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card payment-method border" data-method="paypal" onclick="selectPaymentMethod('paypal')">
                                    <div class="card-body text-center">
                                        <i class="fab fa-paypal fa-2x text-info mb-2"></i>
                                        <h6>PayPal</h6>
                                        <small class="text-muted">Pay with PayPal account</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card payment-method border" data-method="bank" onclick="selectPaymentMethod('bank')">
                                    <div class="card-body text-center">
                                        <i class="fas fa-university fa-2x text-success mb-2"></i>
                                        <h6>Bank Transfer</h6>
                                        <small class="text-muted">Direct bank transfer</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card payment-method border" data-method="crypto" onclick="selectPaymentMethod('crypto')">
                                    <div class="card-body text-center">
                                        <i class="fab fa-bitcoin fa-2x text-warning mb-2"></i>
                                        <h6>Cryptocurrency</h6>
                                        <small class="text-muted">BTC, ETH, USDT</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Payment Form -->
                        <div id="card-form" class="payment-form" style="display: none;">
                            <h6>Card Information</h6>
                            <form id="card-payment-form">
                                <input type="hidden" name="session_id" value="<?php echo $sessionId; ?>">
                                <input type="hidden" name="payment_method" value="card">

                                <div class="mb-3">
                                    <label for="card-number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card-number" placeholder="1234 5678 9012 3456" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiry-date" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiry-date" placeholder="MM/YY" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="card-name" class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" id="card-name" placeholder="John Doe" required>
                                </div>
                            </form>
                        </div>

                        <!-- PayPal Form -->
                        <div id="paypal-form" class="payment-form" style="display: none;">
                            <div class="text-center">
                                <p>You will be redirected to PayPal to complete your payment securely.</p>
                                <button type="button" class="btn btn-info" onclick="processPayPalPayment()">
                                    <i class="fab fa-paypal"></i> Continue with PayPal
                                </button>
                            </div>
                        </div>

                        <!-- Bank Transfer Form -->
                        <div id="bank-form" class="payment-form" style="display: none;">
                            <div class="alert alert-info">
                                <h6>Bank Transfer Instructions</h6>
                                <p>Please transfer the amount to the following account:</p>
                                <strong>Account Name:</strong> School Management System<br>
                                <strong>Account Number:</strong> 1234-5678-9012<br>
                                <strong>Bank:</strong> ABC Bank<br>
                                <strong>Reference:</strong> <?php echo $sessionId; ?>
                            </div>
                            <div class="mb-3">
                                <label for="transaction-id" class="form-label">Transaction ID/Reference</label>
                                <input type="text" class="form-control" id="transaction-id" placeholder="Enter bank transaction ID">
                            </div>
                        </div>

                        <!-- Crypto Form -->
                        <div id="crypto-form" class="payment-form" style="display: none;">
                            <div class="alert alert-warning">
                                <h6>Cryptocurrency Payment</h6>
                                <p>Send exactly <strong>$<?php echo number_format($session['amount'], 2); ?> USD</strong> worth of cryptocurrency to:</p>
                                <strong>Wallet Address:</strong><br>
                                <code>1A2B3C4D5E6F7G8H9I0J1K2L3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z</code>
                                <br><small class="text-muted">Supported: BTC, ETH, USDT</small>
                            </div>
                            <div class="mb-3">
                                <label for="crypto-tx" class="form-label">Transaction Hash</label>
                                <input type="text" class="form-control" id="crypto-tx" placeholder="Enter transaction hash">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="button" class="btn btn-success btn-lg" id="process-payment-btn" disabled>
                                <i class="fas fa-lock"></i> Process Payment
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.close()">
                                Cancel Payment
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <small class="secure-badge">
                            <i class="fas fa-shield-alt"></i> 256-bit SSL Encrypted | PCI DSS Compliant
                        </small>
                    </div>
                </div>

                <!-- Processing Modal -->
                <div class="modal fade" id="processingModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center p-4">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                                <h5>Processing Payment</h5>
                                <p class="text-muted">Please wait while we process your payment securely...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedMethod = null;

        function selectPaymentMethod(method) {
            // Remove selected class from all methods
            document.querySelectorAll('.payment-method').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked method
            document.querySelector(`[data-method="${method}"]`).classList.add('selected');

            // Hide all forms
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });

            // Show selected form
            document.getElementById(`${method}-form`).style.display = 'block';

            selectedMethod = method;
            document.getElementById('process-payment-btn').disabled = false;
        }

        document.getElementById('process-payment-btn').addEventListener('click', function() {
            if (!selectedMethod) {
                alert('Please select a payment method');
                return;
            }

            // Show processing modal
            const processingModal = new bootstrap.Modal(document.getElementById('processingModal'));
            processingModal.show();

            // Prepare form data
            const formData = new FormData();
            formData.append('session_id', '<?php echo $sessionId; ?>');
            formData.append('payment_method', selectedMethod);

            // Add method-specific data
            if (selectedMethod === 'card') {
                formData.append('card_number', document.getElementById('card-number').value);
                formData.append('expiry_date', document.getElementById('expiry-date').value);
                formData.append('cvv', document.getElementById('cvv').value);
                formData.append('card_name', document.getElementById('card-name').value);
            } else if (selectedMethod === 'bank') {
                formData.append('transaction_id', document.getElementById('transaction-id').value);
            } else if (selectedMethod === 'crypto') {
                formData.append('transaction_hash', document.getElementById('crypto-tx').value);
            }

            // Process payment
            fetch('/cashier/api/payment/process', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                processingModal.hide();

                if (result.success) {
                    // Show success message and redirect
                    alert('Payment processed successfully!\nReceipt Number: ' + result.receipt_number);

                    // Close window or redirect to success page
                    if (window.opener) {
                        window.opener.location.reload(); // Refresh parent window
                        window.close();
                    } else {
                        window.location.href = '/cashier/dashboard';
                    }
                } else {
                    alert('Payment failed: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => {
                processingModal.hide();
                console.error('Payment processing error:', error);
                alert('Payment processing failed. Please try again.');
            });
        });

        function processPayPalPayment() {
            // In a real implementation, this would redirect to PayPal
            alert('PayPal integration would redirect to PayPal payment page here.');

            // For demo, simulate PayPal success
            const processingModal = new bootstrap.Modal(document.getElementById('processingModal'));
            processingModal.show();

            setTimeout(() => {
                processingModal.hide();
                alert('PayPal payment simulated successfully!');
                if (window.opener) {
                    window.opener.location.reload();
                    window.close();
                }
            }, 2000);
        }

        // Auto-format card number
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Auto-format expiry date
        document.getElementById('expiry-date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>