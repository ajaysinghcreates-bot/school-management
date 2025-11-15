<?php
class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        Session::getInstance(); // Initialize session
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->showLoginForm();
        }
    }

    private function showLoginForm() {
        // Generate CSRF token if not exists
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        include 'views/auth/login/index.php';
    }

    private function handleLogin() {
        header('Content-Type: application/json');

        // CSRF check
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Server-side validation
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            return;
        }

        // Authenticate user
        try {
            $stmt = $this->db->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Remember me
                if ($remember) {
                    setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 days
                }

                // Role-based redirection
                $redirects = [
                    'admin' => '/admin/dashboard',
                    'teacher' => '/teacher/dashboard',
                    'cashier' => '/cashier/dashboard',
                    'student' => '/student/dashboard',
                    'parent' => '/parent/dashboard'
                ];

                $redirect = $redirects[$user['role']] ?? '/';

                echo json_encode(['success' => true, 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Login failed: ' . $e->getMessage()]);
        }
    }
}
?>