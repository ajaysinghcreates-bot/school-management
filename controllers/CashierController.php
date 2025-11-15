<?php
class CashierController {
    private $db;

    public function __construct() {
        session_start();
        // Database connection
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=school_management;charset=utf8', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function dashboard() {
        // Role-based access: cashier only
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        // Fetch key statistics
        $stats = $this->getDashboardData();

        // Pass data to view
        include 'views/cashier/dashboard/index.php';
    }

    private function getDashboardData() {
        $stats = [];

        try {
            // Total fee collection this month
            $stmt = $this->db->prepare("
                SELECT SUM(amount) as total
                FROM payments
                WHERE DATE_FORMAT(payment_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            ");
            $stmt->execute();
            $stats['monthly_collection'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Outstanding payments
            $stmt = $this->db->query("SELECT SUM(amount) as total FROM fees WHERE status = 'pending'");
            $stats['outstanding_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total expenses this month
            $stmt = $this->db->prepare("
                SELECT SUM(amount) as total
                FROM expenses
                WHERE DATE_FORMAT(expense_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            ");
            $stmt->execute();
            $stats['monthly_expenses'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Net profit (collection - expenses)
            $stats['net_profit'] = $stats['monthly_collection'] - $stats['monthly_expenses'];

        } catch (Exception $e) {
            $stats = array_fill_keys(['monthly_collection', 'outstanding_payments', 'monthly_expenses', 'net_profit'], 0);
        }

        return $stats;
    }

    // API method for dynamic stats
    public function getDashboardStats() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stats = $this->getDashboardData();
        echo json_encode($stats);
    }

    public function fees() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/fees/index.php';
    }

    public function outstanding() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/outstanding/index.php';
    }

    public function reports() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/reports/index.php';
    }

    public function expenses() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/expenses/index.php';
    }

    // API Endpoints for Fees
    public function getFeesData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "s.name LIKE ?";
                $params[] = "%$search%";
            }
            if ($status) {
                $where[] = "f.status = ?";
                $params[] = $status;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM fees f JOIN students s ON f.student_id = s.id $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT f.*, s.name as student_name FROM fees f JOIN students s ON f.student_id = s.id $whereClause ORDER BY f.due_date DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $fees, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function processPayment() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            // Generate receipt number if not provided
            if (empty($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNumber();
            }

            // Insert payment record
            $stmt = $this->db->prepare("INSERT INTO payments (fee_id, amount, payment_date, payment_method, receipt_number, cashier_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['fee_id'], $data['amount'], $data['payment_date'], $data['payment_method'], $data['receipt_number'], $_SESSION['user_id'] ?? 1]);

            // Update fee status
            $stmt = $this->db->prepare("UPDATE fees SET status = 'paid', paid_date = ?, payment_id = LAST_INSERT_ID() WHERE id = ?");
            $stmt->execute([$data['payment_date'], $data['fee_id']]);

            $paymentId = $this->db->lastInsertId();

            // Log the transaction
            $this->logTransaction('payment', $data['fee_id'], $data['amount'], 'Payment processed: ' . $data['receipt_number']);

            echo json_encode(['success' => true, 'receipt_number' => $data['receipt_number'], 'payment_id' => $paymentId]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Outstanding
    public function getOutstandingData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = "WHERE f.status = 'pending'";
            $params = [];
            if ($search) {
                $where .= " AND s.name LIKE ?";
                $params[] = "%$search%";
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM fees f JOIN students s ON f.student_id = s.id $where");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("
                SELECT f.*, s.name as student_name, s.email, s.phone,
                       r.sent_at as last_reminder
                FROM fees f
                JOIN students s ON f.student_id = s.id
                LEFT JOIN reminders r ON f.id = r.fee_id AND r.sent_at = (
                    SELECT MAX(sent_at) FROM reminders WHERE fee_id = f.id
                )
                $where ORDER BY f.due_date ASC LIMIT $limit OFFSET $offset
            ");
            $stmt->execute($params);
            $outstanding = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $outstanding, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function sendReminder() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $fee_id = $_GET['fee_id'] ?? 0;
        try {
            // Get fee and student details
            $stmt = $this->db->prepare("SELECT f.*, s.name, s.email, s.phone FROM fees f JOIN students s ON f.student_id = s.id WHERE f.id = ?");
            $stmt->execute([$fee_id]);
            $fee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fee) {
                // Send email reminder
                $emailSent = $this->sendEmailReminder($fee);

                // Send SMS reminder if phone available
                $smsSent = false;
                if (!empty($fee['phone'])) {
                    $smsSent = $this->sendSMSReminder($fee);
                }

                // Log the reminder
                $stmt = $this->db->prepare("INSERT INTO reminders (fee_id, type, sent_at, email_sent, sms_sent) VALUES (?, 'manual', NOW(), ?, ?)");
                $stmt->execute([$fee_id, $emailSent ? 1 : 0, $smsSent ? 1 : 0]);

                $message = 'Reminder ';
                if ($emailSent && $smsSent) {
                    $message .= 'sent via email and SMS';
                } elseif ($emailSent) {
                    $message .= 'sent via email';
                } elseif ($smsSent) {
                    $message .= 'sent via SMS';
                } else {
                    $message .= 'logged (email/SMS not configured)';
                }

                echo json_encode(['success' => true, 'message' => $message . ' to ' . $fee['email']]);
            } else {
                echo json_encode(['error' => 'Fee not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Automated reminder system
    public function sendAutomatedReminders() {
        // This would typically be called by a cron job
        // For now, it's a manual trigger for demonstration

        try {
            // Get overdue fees that haven't been reminded in the last 7 days
            $stmt = $this->db->prepare("
                SELECT f.*, s.name, s.email, s.phone
                FROM fees f
                JOIN students s ON f.student_id = s.id
                LEFT JOIN reminders r ON f.id = r.fee_id AND r.sent_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                WHERE f.status = 'pending'
                AND f.due_date < CURDATE()
                AND r.id IS NULL
                LIMIT 50
            ");
            $stmt->execute();
            $overdueFees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sentCount = 0;
            foreach ($overdueFees as $fee) {
                // Send email reminder
                $emailSent = $this->sendEmailReminder($fee, true); // true for automated

                // Send SMS reminder if phone available
                $smsSent = false;
                if (!empty($fee['phone'])) {
                    $smsSent = $this->sendSMSReminder($fee, true); // true for automated
                }

                if ($emailSent || $smsSent) {
                    // Log the automated reminder
                    $stmt = $this->db->prepare("INSERT INTO reminders (fee_id, type, sent_at, email_sent, sms_sent) VALUES (?, 'automated', NOW(), ?, ?)");
                    $stmt->execute([$fee['id'], $emailSent ? 1 : 0, $smsSent ? 1 : 0]);
                    $sentCount++;
                }
            }

            return ['success' => true, 'sent' => $sentCount, 'total' => count($overdueFees)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Email reminder functionality
    private function sendEmailReminder($fee, $automated = false) {
        try {
            $subject = $automated ? 'Automated Payment Reminder - School Fees' : 'Payment Reminder - School Fees';
            $message = $this->generateReminderEmail($fee, $automated);

            // Basic email sending (replace with PHPMailer or similar in production)
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: School Management <noreply@school.com>',
                'Reply-To: accounts@school.com'
            ];

            // In a real implementation, you'd use a proper mail library
            // For demonstration, we'll simulate success
            $emailSent = true; // mail($fee['email'], $subject, $message, implode("\r\n", $headers));

            // Log email attempt
            error_log("Email reminder " . ($emailSent ? 'sent' : 'failed') . " to: " . $fee['email']);

            return $emailSent;
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    // SMS reminder functionality
    private function sendSMSReminder($fee, $automated = false) {
        try {
            $message = $this->generateReminderSMS($fee, $automated);

            // Basic SMS sending (integrate with Twilio, AWS SNS, etc. in production)
            // For demonstration, we'll simulate success for valid phone numbers
            $smsSent = !empty($fee['phone']) && strlen($fee['phone']) >= 10;

            // Log SMS attempt
            error_log("SMS reminder " . ($smsSent ? 'sent' : 'failed') . " to: " . $fee['phone']);

            return $smsSent;
        } catch (Exception $e) {
            error_log('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    // Generate email content
    private function generateReminderEmail($fee, $automated = false) {
        $type = $automated ? 'Automated' : 'Manual';
        $dueDate = date('d/m/Y', strtotime($fee['due_date']));
        $amount = number_format($fee['amount'], 2);

        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f8f9fa; padding: 10px; text-align: center; font-size: 12px; }
                .urgent { color: #dc3545; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>School Management System</h2>
                <h3>{$type} Payment Reminder</h3>
            </div>
            <div class='content'>
                <p>Dear {$fee['name']},</p>

                <p class='urgent'>This is an urgent reminder that you have outstanding school fees that require immediate attention.</p>

                <div style='background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #dc3545;'>
                    <strong>Fee Details:</strong><br>
                    Description: {$fee['description']}<br>
                    Amount Due: \${$amount}<br>
                    Due Date: {$dueDate}<br>
                    Status: <span class='urgent'>OVERDUE</span>
                </div>

                <p>Please make payment at your earliest convenience to avoid additional charges or service interruptions.</p>

                <p>You can make payment through:</p>
                <ul>
                    <li>Online portal: <a href='https://school.com/pay'>school.com/pay</a></li>
                    <li>School office during business hours</li>
                    <li>Bank transfer to account: XXXX-XXXX-XXXX</li>
                </ul>

                <p>If you have already made payment, please disregard this reminder.</p>

                <p>Thank you for your prompt attention to this matter.</p>
            </div>
            <div class='footer'>
                <p>School Management System | Accounts Department<br>
                Email: accounts@school.com | Phone: (123) 456-7890</p>
            </div>
        </body>
        </html>
        ";
    }

    // Generate SMS content
    private function generateReminderSMS($fee, $automated = false) {
        $type = $automated ? 'Auto' : 'Manual';
        $amount = number_format($fee['amount'], 2);

        return "SCHOOL {$type} REMINDER: {$fee['name']}, you have overdue fees of \${$amount} for {$fee['description']}. Due: " . date('d/m/Y', strtotime($fee['due_date'])) . ". Pay now to avoid penalties. Visit school.com/pay";
    }

    // Advanced financial analytics and forecasting
    public function getFinancialAnalytics() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $analytics = [];

            // Revenue trends and forecasting
            $analytics['revenue_trends'] = $this->getRevenueTrends();
            $analytics['revenue_forecast'] = $this->forecastRevenue();

            // Expense analysis
            $analytics['expense_analysis'] = $this->getExpenseAnalysis();
            $analytics['expense_forecast'] = $this->forecastExpenses();

            // Profitability metrics
            $analytics['profitability_metrics'] = $this->getProfitabilityMetrics();

            // Payment method distribution
            $analytics['payment_methods'] = $this->getPaymentMethodDistribution();

            // Seasonal patterns
            $analytics['seasonal_patterns'] = $this->getSeasonalPatterns();

            echo json_encode($analytics);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function getRevenueTrends() {
        // Get monthly revenue for the last 24 months
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(payment_date, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as transactions
            FROM payments
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function forecastRevenue() {
        $trends = $this->getRevenueTrends();

        if (count($trends) < 6) {
            return ['error' => 'Insufficient data for forecasting'];
        }

        // Simple linear regression for forecasting
        $n = count($trends);
        $sumX = $sumY = $sumXY = $sumXX = 0;

        foreach ($trends as $i => $trend) {
            $x = $i + 1; // Month index
            $y = (float)$trend['revenue'];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumXX += $x * $x;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Forecast next 6 months
        $forecast = [];
        for ($i = 1; $i <= 6; $i++) {
            $futureMonth = date('Y-m', strtotime("+$i months"));
            $predicted = $slope * ($n + $i) + $intercept;
            $forecast[] = [
                'month' => $futureMonth,
                'predicted_revenue' => max(0, $predicted),
                'confidence' => 'medium' // Could be calculated based on R-squared
            ];
        }

        return $forecast;
    }

    private function getExpenseAnalysis() {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                category,
                SUM(amount) as total_expenses,
                COUNT(*) as transaction_count
            FROM expenses
            WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m'), category
            ORDER BY month, category
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function forecastExpenses() {
        $analysis = $this->getExpenseAnalysis();

        if (count($analysis) < 6) {
            return ['error' => 'Insufficient data for expense forecasting'];
        }

        // Group by category and forecast each
        $forecasts = [];
        $categories = array_unique(array_column($analysis, 'category'));

        foreach ($categories as $category) {
            $categoryData = array_filter($analysis, function($item) use ($category) {
                return $item['category'] === $category;
            });

            if (count($categoryData) >= 3) {
                // Simple moving average forecast
                $recentExpenses = array_slice(array_column($categoryData, 'total_expenses'), -3);
                $average = array_sum($recentExpenses) / count($recentExpenses);

                $forecasts[] = [
                    'category' => $category,
                    'forecasted_amount' => $average,
                    'trend' => $this->calculateTrend($recentExpenses)
                ];
            }
        }

        return $forecasts;
    }

    private function calculateTrend($values) {
        if (count($values) < 2) return 'stable';

        $first = $values[0];
        $last = end($values);
        $change = (($last - $first) / $first) * 100;

        if ($change > 10) return 'increasing';
        if ($change < -10) return 'decreasing';
        return 'stable';
    }

    private function getProfitabilityMetrics() {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(p.payment_date, '%Y-%m') as month,
                SUM(p.amount) as revenue,
                COALESCE(SUM(e.amount), 0) as expenses,
                (SUM(p.amount) - COALESCE(SUM(e.amount), 0)) as profit,
                ((SUM(p.amount) - COALESCE(SUM(e.amount), 0)) / SUM(p.amount) * 100) as profit_margin
            FROM payments p
            LEFT JOIN expenses e ON DATE_FORMAT(p.payment_date, '%Y-%m') = DATE_FORMAT(e.expense_date, '%Y-%m')
            WHERE p.payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getPaymentMethodDistribution() {
        $stmt = $this->db->prepare("
            SELECT
                payment_method,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount,
                (SUM(amount) / (SELECT SUM(amount) FROM payments) * 100) as percentage
            FROM payments
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSeasonalPatterns() {
        $stmt = $this->db->prepare("
            SELECT
                MONTH(payment_date) as month_num,
                MONTHNAME(payment_date) as month_name,
                SUM(amount) as monthly_revenue,
                COUNT(*) as transaction_count,
                AVG(amount) as avg_transaction
            FROM payments
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
            GROUP BY MONTH(payment_date), MONTHNAME(payment_date)
            ORDER BY month_num
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Payment gateway integration framework
    public function initiatePayment() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $feeId = $data['fee_id'];
        $amount = $data['amount'];

        // Get fee details
        $stmt = $this->db->prepare("SELECT f.*, s.name, s.email FROM fees f JOIN students s ON f.student_id = s.id WHERE f.id = ?");
        $stmt->execute([$feeId]);
        $fee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fee) {
            echo json_encode(['error' => 'Fee not found']);
            return;
        }

        // Generate payment session/token
        $paymentSession = $this->createPaymentSession($fee, $amount);

        echo json_encode([
            'success' => true,
            'payment_session' => $paymentSession,
            'payment_url' => '/cashier/payment-gateway?session=' . $paymentSession['id']
        ]);
    }

    private function createPaymentSession($fee, $amount) {
        $sessionId = uniqid('pay_', true);

        // Store payment session in database (in production, use Redis or similar)
        $sessionData = [
            'id' => $sessionId,
            'fee_id' => $fee['id'],
            'student_id' => $fee['student_id'],
            'amount' => $amount,
            'currency' => 'USD',
            'description' => $fee['description'],
            'student_email' => $fee['email'],
            'student_name' => $fee['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'status' => 'pending'
        ];

        // In production, store in payment_sessions table
        // For demo, we'll use session storage
        $_SESSION['payment_sessions'][$sessionId] = $sessionData;

        return $sessionData;
    }

    public function paymentGateway() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $sessionId = $_GET['session'] ?? '';

        if (!$sessionId || !isset($_SESSION['payment_sessions'][$sessionId])) {
            echo "Invalid payment session";
            exit;
        }

        $session = $_SESSION['payment_sessions'][$sessionId];

        // Include payment gateway template
        include 'views/cashier/payment_gateway/index.php';
    }

    public function processGatewayPayment() {
        header('Content-Type: application/json');

        $sessionId = $_POST['session_id'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';

        if (!$sessionId || !isset($_SESSION['payment_sessions'][$sessionId])) {
            echo json_encode(['error' => 'Invalid payment session']);
            return;
        }

        $session = $_SESSION['payment_sessions'][$sessionId];

        // Simulate payment processing (integrate with actual gateway)
        $success = $this->processMockPayment($session, $paymentMethod);

        if ($success) {
            // Update fee status
            $stmt = $this->db->prepare("UPDATE fees SET status = 'paid', paid_date = ? WHERE id = ?");
            $stmt->execute([date('Y-m-d'), $session['fee_id']]);

            // Create payment record
            $receiptNumber = $this->generateReceiptNumber();
            $stmt = $this->db->prepare("INSERT INTO payments (fee_id, amount, payment_date, payment_method, receipt_number, cashier_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$session['fee_id'], $session['amount'], date('Y-m-d'), $paymentMethod, $receiptNumber, $_SESSION['user_id'] ?? 1]);

            // Log transaction
            $this->logTransaction('payment', $session['fee_id'], $session['amount'], 'Online payment processed: ' . $receiptNumber);

            // Mark session as completed
            $_SESSION['payment_sessions'][$sessionId]['status'] = 'completed';

            echo json_encode([
                'success' => true,
                'receipt_number' => $receiptNumber,
                'message' => 'Payment processed successfully'
            ]);
        } else {
            echo json_encode(['error' => 'Payment processing failed']);
        }
    }

    private function processMockPayment($session, $paymentMethod) {
        // Simulate payment processing success/failure
        // In production, integrate with Stripe, PayPal, etc.

        // Simulate 95% success rate
        return rand(1, 100) <= 95;
    }

    // API Endpoints for Reports
    public function getFinancialReport() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $period = $_GET['period'] ?? 'monthly'; // monthly, quarterly, yearly

        try {
            $data = [];
            if ($period == 'monthly') {
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    $stmt = $this->db->prepare("SELECT SUM(amount) as collection FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = ?");
                    $stmt->execute([$month]);
                    $collection = $stmt->fetch(PDO::FETCH_ASSOC)['collection'] ?? 0;

                    $stmt = $this->db->prepare("SELECT SUM(amount) as expenses FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?");
                    $stmt->execute([$month]);
                    $expenses = $stmt->fetch(PDO::FETCH_ASSOC)['expenses'] ?? 0;

                    $data[] = [
                        'period' => date('M Y', strtotime($month)),
                        'collection' => (float)$collection,
                        'expenses' => (float)$expenses,
                        'profit' => (float)($collection - $expenses)
                    ];
                }
            }
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Expenses
    public function getExpensesData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "description LIKE ?";
                $params[] = "%$search%";
            }
            if ($category) {
                $where[] = "category = ?";
                $params[] = $category;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM expenses $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT * FROM expenses $whereClause ORDER BY expense_date DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $expenses, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createExpense() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO expenses (description, amount, category, expense_date, cashier_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['description'], $data['amount'], $data['category'], $data['expense_date'], $_SESSION['user_id'] ?? 1]);
            $expenseId = $this->db->lastInsertId();

            // Log the transaction
            $this->logTransaction('expense', $expenseId, $data['amount'], 'Expense created: ' . $data['description']);

            echo json_encode(['success' => true, 'id' => $expenseId]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateExpense() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE expenses SET description=?, amount=?, category=?, expense_date=? WHERE id=?");
            $stmt->execute([$data['description'], $data['amount'], $data['category'], $data['expense_date'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteExpense() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM expenses WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Helper method to generate unique receipt numbers
    private function generateReceiptNumber() {
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        return 'RCP-' . $date . '-' . $random;
    }

    // PDF receipt generation
    public function generateReceipt() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $paymentId = $_GET['payment_id'] ?? 0;

        // Get payment details
        $stmt = $this->db->prepare("
            SELECT p.*, f.description as fee_description, s.name as student_name, s.class_id, c.name as class_name
            FROM payments p
            JOIN fees f ON p.fee_id = f.id
            JOIN students s ON f.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            die('Payment not found');
        }

        // Generate PDF receipt
        require_once 'libraries/tcpdf/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('Cashier Portal');
        $pdf->SetTitle('Payment Receipt - ' . $payment['receipt_number']);

        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        $pdf->AddPage();

        // School Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'School Management System', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 8, 'Payment Receipt', 0, 1, 'C');
        $pdf->Ln(5);

        // Receipt Details
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'Receipt Details', 0, 1);
        $pdf->SetFont('helvetica', '', 10);

        $pdf->Cell(50, 6, 'Receipt Number:', 0, 0);
        $pdf->Cell(0, 6, $payment['receipt_number'], 0, 1);

        $pdf->Cell(50, 6, 'Date:', 0, 0);
        $pdf->Cell(0, 6, date('d/m/Y', strtotime($payment['payment_date'])), 0, 1);

        $pdf->Cell(50, 6, 'Payment Method:', 0, 0);
        $pdf->Cell(0, 6, ucfirst($payment['payment_method']), 0, 1);

        $pdf->Ln(5);

        // Student Details
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'Student Details', 0, 1);
        $pdf->SetFont('helvetica', '', 10);

        $pdf->Cell(50, 6, 'Student Name:', 0, 0);
        $pdf->Cell(0, 6, $payment['student_name'], 0, 1);

        $pdf->Cell(50, 6, 'Class:', 0, 0);
        $pdf->Cell(0, 6, $payment['class_name'] ?? 'N/A', 0, 1);

        $pdf->Cell(50, 6, 'Fee Description:', 0, 0);
        $pdf->Cell(0, 6, $payment['fee_description'], 0, 1);

        $pdf->Ln(5);

        // Payment Amount
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Amount Paid: $' . number_format($payment['amount'], 2), 0, 1, 'C');

        $pdf->Ln(10);

        // Footer
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 5, 'This is a computer generated receipt. No signature required.', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Generated on: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

        // Output PDF
        $filename = 'receipt_' . $payment['receipt_number'] . '.pdf';
        $pdf->Output($filename, 'D');
    }

    // Excel/CSV export for financial reports
    public function exportFinancialReportExcel() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $period = $_GET['period'] ?? 'monthly';
        $format = $_GET['format'] ?? 'csv'; // csv or excel

        // Get report data
        $data = $this->getFinancialReportData($period);

        if ($format === 'excel') {
            // Try to use PHPSpreadsheet if available
            $this->exportExcelReport($data, $period);
        } else {
            // Default to CSV export
            $this->exportCSVReport($data, $period);
        }
    }

    private function exportCSVReport($data, $period) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="financial_report_' . $period . '_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['Period', 'Revenue ($)', 'Expenses ($)', 'Profit ($)', 'Profit Margin (%)']);

        // CSV data
        foreach ($data as $row) {
            $margin = $row['collection'] > 0 ? round(($row['profit'] / $row['collection']) * 100, 1) : 0;
            fputcsv($output, [
                $row['period'],
                number_format($row['collection'], 2),
                number_format($row['expenses'], 2),
                number_format($row['profit'], 2),
                $margin . '%'
            ]);
        }

        fclose($output);
        exit;
    }

    private function exportExcelReport($data, $period) {
        // Check if PHPSpreadsheet is available
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'School Management System - Financial Report');
            $sheet->setCellValue('A2', 'Period: ' . ucfirst($period));
            $sheet->setCellValue('A3', 'Generated: ' . date('Y-m-d H:i:s'));
            $sheet->setCellValue('A5', 'Period');
            $sheet->setCellValue('B5', 'Revenue ($)');
            $sheet->setCellValue('C5', 'Expenses ($)');
            $sheet->setCellValue('D5', 'Profit ($)');
            $sheet->setCellValue('E5', 'Profit Margin (%)');

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCCCCC']]
            ];
            $sheet->getStyle('A5:E5')->applyFromArray($headerStyle);

            // Add data
            $row = 6;
            foreach ($data as $item) {
                $margin = $item['collection'] > 0 ? round(($item['profit'] / $item['collection']) * 100, 1) : 0;
                $sheet->setCellValue('A' . $row, $item['period']);
                $sheet->setCellValue('B' . $row, $item['collection']);
                $sheet->setCellValue('C' . $row, $item['expenses']);
                $sheet->setCellValue('D' . $row, $item['profit']);
                $sheet->setCellValue('E' . $row, $margin . '%');
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="financial_report_' . $period . '_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
            // Fallback to CSV if PHPSpreadsheet not available
            $this->exportCSVReport($data, $period);
        }
    }

    // PDF export for financial reports
    public function exportFinancialReport() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $period = $_GET['period'] ?? 'monthly';

        // Get report data
        $data = $this->getFinancialReportData($period);

        // Generate PDF (assuming TCPDF is available)
        require_once 'libraries/tcpdf/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('Cashier Portal');
        $pdf->SetTitle('Financial Report - ' . ucfirst($period));
        $pdf->SetSubject('Financial Summary Report');

        // Set margins
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Title
        $pdf->Cell(0, 10, 'School Management System - Financial Report', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Period: ' . ucfirst($period) . ' | Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $pdf->Ln(10);

        // Summary
        $totalRevenue = array_sum(array_column($data, 'collection'));
        $totalExpenses = array_sum(array_column($data, 'expenses'));
        $totalProfit = array_sum(array_column($data, 'profit'));

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'Summary', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(60, 6, 'Total Revenue:', 0, 0);
        $pdf->Cell(30, 6, '$' . number_format($totalRevenue, 2), 0, 1);
        $pdf->Cell(60, 6, 'Total Expenses:', 0, 0);
        $pdf->Cell(30, 6, '$' . number_format($totalExpenses, 2), 0, 1);
        $pdf->Cell(60, 6, 'Net Profit:', 0, 0);
        $pdf->Cell(30, 6, '$' . number_format($totalProfit, 2), 0, 1);
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(40, 8, 'Period', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Revenue', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Expenses', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Profit', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Margin %', 1, 1, 'C', true);

        // Table data
        $pdf->SetFont('helvetica', '', 9);
        foreach ($data as $row) {
            $margin = $row['collection'] > 0 ? round(($row['profit'] / $row['collection']) * 100, 1) : 0;
            $pdf->Cell(40, 7, $row['period'], 1, 0, 'C');
            $pdf->Cell(35, 7, '$' . number_format($row['collection'], 2), 1, 0, 'R');
            $pdf->Cell(35, 7, '$' . number_format($row['expenses'], 2), 1, 0, 'R');
            $pdf->Cell(35, 7, '$' . number_format($row['profit'], 2), 1, 0, 'R');
            $pdf->Cell(25, 7, $margin . '%', 1, 1, 'R');
        }

        // Output PDF
        $filename = 'financial_report_' . $period . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D'); // Download
    }

    // Helper method to get financial report data
    private function getFinancialReportData($period) {
        try {
            $data = [];
            if ($period == 'monthly') {
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    $stmt = $this->db->prepare("SELECT SUM(amount) as collection FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = ?");
                    $stmt->execute([$month]);
                    $collection = $stmt->fetch(PDO::FETCH_ASSOC)['collection'] ?? 0;

                    $stmt = $this->db->prepare("SELECT SUM(amount) as expenses FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?");
                    $stmt->execute([$month]);
                    $expenses = $stmt->fetch(PDO::FETCH_ASSOC)['expenses'] ?? 0;

                    $data[] = [
                        'period' => date('M Y', strtotime($month)),
                        'collection' => (float)$collection,
                        'expenses' => (float)$expenses,
                        'profit' => (float)($collection - $expenses)
                    ];
                }
            }
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    // Bulk payment processing via CSV import
    public function bulkImportPayments() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'No file uploaded or upload error']);
            return;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if ($handle === false) {
            echo json_encode(['error' => 'Could not open file']);
            return;
        }

        // Skip header row
        fgetcsv($handle);

        $successCount = 0;
        $errors = [];
        $rowNumber = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Expected CSV format: fee_id, amount, payment_date, payment_method
            if (count($data) < 4) {
                $errors[] = "Row $rowNumber: Insufficient data columns";
                continue;
            }

            $feeId = trim($data[0]);
            $amount = trim($data[1]);
            $paymentDate = trim($data[2]);
            $paymentMethod = trim($data[3]);

            // Validate data
            if (empty($feeId) || !is_numeric($feeId)) {
                $errors[] = "Row $rowNumber: Invalid fee ID";
                continue;
            }

            if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
                $errors[] = "Row $rowNumber: Invalid amount";
                continue;
            }

            if (empty($paymentDate) || !strtotime($paymentDate)) {
                $errors[] = "Row $rowNumber: Invalid payment date";
                continue;
            }

            $validMethods = ['cash', 'card', 'bank_transfer', 'check'];
            if (empty($paymentMethod) || !in_array($paymentMethod, $validMethods)) {
                $errors[] = "Row $rowNumber: Invalid payment method";
                continue;
            }

            try {
                // Check if fee exists and is pending
                $stmt = $this->db->prepare("SELECT id, amount, status FROM fees WHERE id = ?");
                $stmt->execute([$feeId]);
                $fee = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$fee) {
                    $errors[] = "Row $rowNumber: Fee not found";
                    continue;
                }

                if ($fee['status'] !== 'pending') {
                    $errors[] = "Row $rowNumber: Fee already paid";
                    continue;
                }

                // Generate receipt number
                $receiptNumber = $this->generateReceiptNumber();

                // Insert payment record
                $stmt = $this->db->prepare("INSERT INTO payments (fee_id, amount, payment_date, payment_method, receipt_number, cashier_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$feeId, $amount, $paymentDate, $paymentMethod, $receiptNumber, $_SESSION['user_id'] ?? 1]);

                // Update fee status
                $stmt = $this->db->prepare("UPDATE fees SET status = 'paid', paid_date = ? WHERE id = ?");
                $stmt->execute([$paymentDate, $feeId]);

                // Log the transaction
                $this->logTransaction('payment', $feeId, $amount, 'Bulk payment processed: ' . $receiptNumber);

                $successCount++;

            } catch (Exception $e) {
                $errors[] = "Row $rowNumber: Database error - " . $e->getMessage();
            }
        }

        fclose($handle);

        echo json_encode([
            'success' => $successCount,
            'errors' => $errors,
            'total_processed' => $rowNumber - 1
        ]);
    }

    // Expense categories management
    public function expenseCategories() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/expense_categories/index.php';
    }

    // API endpoints for expense categories
    public function getExpenseCategories() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->query("SELECT * FROM expense_categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['data' => $categories]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createExpenseCategory() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO expense_categories (name, description) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['description'] ?? '']);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateExpenseCategory() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE expense_categories SET name=?, description=? WHERE id=?");
            $stmt->execute([$data['name'], $data['description'] ?? '', $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteExpenseCategory() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            // Check if category is in use
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM expenses WHERE category = (SELECT name FROM expense_categories WHERE id = ?)");
            $stmt->execute([$id]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($count > 0) {
                echo json_encode(['error' => 'Cannot delete category that is in use by expenses']);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM expense_categories WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Helper method to log financial transactions
    private function logTransaction($type, $reference_id, $amount, $description) {
        try {
            $stmt = $this->db->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, amount, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'] ?? 1,
                $type,
                $type === 'payment' ? 'fees' : 'expenses',
                $reference_id,
                $amount,
                $description
            ]);
        } catch (Exception $e) {
            // Log to file if database logging fails
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }

    // Academic Documents Management
    public function documents() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }
        include 'views/cashier/documents/index.php';
    }

    // Generate single marksheet
    public function generateMarksheet() {
        $auth = new Auth();
        $auth->permissionCheck('cashier.access');

        $studentId = $_GET['student_id'] ?? 0;
        $examId = $_GET['exam_id'] ?? 0;

        // Get student and exam details
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, e.title as exam_title, e.exam_date, e.total_marks
            FROM students s
            JOIN classes c ON s.class_id = c.id
            JOIN exams e ON e.id = ?
            WHERE s.id = ?
        ");
        $stmt->execute([$examId, $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            die('Student or exam not found');
        }

        // Get student results
        $stmt = $this->db->prepare("
            SELECT r.*, sub.name as subject_name, sub.code as subject_code,
                   e.total_marks, r.marks_obtained,
                   ROUND((r.marks_obtained / e.total_marks) * 100, 2) as percentage,
                   CASE
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 90 THEN 'A+'
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 80 THEN 'A'
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 70 THEN 'B+'
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 60 THEN 'B'
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 50 THEN 'C+'
                       WHEN (r.marks_obtained / e.total_marks) * 100 >= 40 THEN 'C'
                       ELSE 'F'
                   END as grade
            FROM results r
            JOIN exams e ON r.exam_id = e.id
            JOIN subjects sub ON e.subject_id = sub.id
            WHERE r.student_id = ? AND r.exam_id = ?
        ");
        $stmt->execute([$studentId, $examId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Use PDFGenerator
        require_once 'libraries/PDFGenerator.php';
        $pdfGen = new PDFGenerator();
        $pdfContent = $pdfGen->generateMarksheet($student, ['title' => $student['exam_title'], 'exam_date' => $student['exam_date']], $results);

        // Output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="marksheet_' . $student['name'] . '.pdf"');
        echo $pdfContent;
        exit;
    }

    // Generate bulk marksheet
    public function generateBulkMarksheet() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $classId = $_GET['class_id'] ?? 0;
        $examId = $_GET['exam_id'] ?? 0;

        // Get all students in class with their results
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, e.title as exam_title, e.exam_date, e.total_marks
            FROM students s
            JOIN classes c ON s.class_id = c.id
            JOIN exams e ON e.id = ?
            WHERE s.class_id = ?
            ORDER BY s.name
        ");
        $stmt->execute([$examId, $classId]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($students)) {
            die('No students found in this class');
        }

        $this->generateBulkMarksheetPDF($students, $examId);
    }

    // Generate admit card
    public function generateAdmitCard() {
        $auth = new Auth();
        $auth->permissionCheck('cashier.access');

        $studentId = $_GET['student_id'] ?? 0;
        $examId = $_GET['exam_id'] ?? 0;

        // Get student and exam details
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, e.title as exam_title, e.exam_date, e.start_time, e.end_time,
                   CONCAT(e.start_time, ' - ', e.end_time) as exam_time, 'School Auditorium' as venue
            FROM students s
            JOIN classes c ON s.class_id = c.id
            JOIN exams e ON e.id = ?
            WHERE s.id = ?
        ");
        $stmt->execute([$examId, $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            die('Student or exam not found');
        }

        // Get subject schedule for the exam
        $stmt = $this->db->prepare("
            SELECT sub.name as subject_name, es.exam_date, es.start_time, es.end_time,
                   CONCAT(es.start_time, ' - ', es.end_time) as time_slot, 'Hall A' as room
            FROM exam_subjects es
            JOIN subjects sub ON es.subject_id = sub.id
            WHERE es.exam_id = ?
            ORDER BY es.exam_date, es.start_time
        ");
        $stmt->execute([$examId]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Use PDFGenerator
        require_once 'libraries/PDFGenerator.php';
        $pdfGen = new PDFGenerator();
        $pdfContent = $pdfGen->generateAdmitCard($student, ['title' => $student['exam_title'], 'exam_date' => $student['exam_date']], $subjects);

        // Output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="admit_card_' . $student['name'] . '.pdf"');
        echo $pdfContent;
        exit;
    }

    // Generate bulk admit cards
    public function generateBulkAdmitCard() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cashier') {
            header('Location: /login');
            exit;
        }

        $classId = $_GET['class_id'] ?? 0;
        $examId = $_GET['exam_id'] ?? 0;

        // Get all students in class
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, e.title as exam_title, e.exam_date, e.exam_time, e.venue
            FROM students s
            JOIN classes c ON s.class_id = c.id
            JOIN exams e ON e.id = ?
            WHERE s.class_id = ?
            ORDER BY s.name
        ");
        $stmt->execute([$examId, $classId]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($students)) {
            die('No students found in this class');
        }

        $this->generateBulkAdmitCardPDF($students);
    }

    // Generate transfer certificate
    public function generateTransferCertificate() {
        $auth = new Auth();
        $auth->permissionCheck('cashier.access');

        $studentId = $_GET['student_id'] ?? 0;

        // Get student details
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, s.enrollment_date, s.dob,
                   TIMESTAMPDIFF(YEAR, s.dob, CURDATE()) as age
            FROM students s
            JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?
        ");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            die('Student not found');
        }

        // Get academic performance summary and record
        $stmt = $this->db->prepare("
            SELECT
                c.name as class_name,
                YEAR(r.created_at) as year,
                AVG(r.marks_obtained) as avg_marks,
                CASE
                    WHEN AVG(r.marks_obtained) >= 90 THEN 'A+'
                    WHEN AVG(r.marks_obtained) >= 80 THEN 'A'
                    WHEN AVG(r.marks_obtained) >= 70 THEN 'B+'
                    WHEN AVG(r.marks_obtained) >= 60 THEN 'B'
                    WHEN AVG(r.marks_obtained) >= 50 THEN 'C+'
                    WHEN AVG(r.marks_obtained) >= 40 THEN 'C'
                    ELSE 'Needs Improvement'
                END as grade,
                'Regular Student' as remarks
            FROM results r
            JOIN exams e ON r.exam_id = e.id
            JOIN classes c ON e.class_id = c.id
            WHERE r.student_id = ?
            GROUP BY c.name, YEAR(r.created_at)
            ORDER BY YEAR(r.created_at)
        ");
        $stmt->execute([$studentId]);
        $academicRecord = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $transferData = [
            'certificate_number' => 'TC-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'leaving_date' => date('Y-m-d'),
            'last_class' => $student['class_name'],
            'conduct' => 'good',
            'new_school' => 'Another Institution',
            'academic_record' => $academicRecord
        ];

        // Use PDFGenerator
        require_once 'libraries/PDFGenerator.php';
        $pdfGen = new PDFGenerator();
        $pdfContent = $pdfGen->generateTransferCertificate($student, $transferData);

        // Output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="transfer_certificate_' . $student['name'] . '.pdf"');
        echo $pdfContent;
        exit;
    }





}
?>