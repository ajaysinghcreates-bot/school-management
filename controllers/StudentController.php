<?php
class StudentController {
    private $db;
    private $student_id;

    public function __construct() {
        session_start();
        // Database connection
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=school_management;charset=utf8', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }

        // Get student ID from session user_id
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT id FROM students WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->student_id = $student ? $student['id'] : null;
        }
    }

    public function dashboard() {
        // Role-based access: student only
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            header('Location: /login');
            exit;
        }

        include 'views/student/dashboard/index.php';
    }

    public function attendance() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            header('Location: /login');
            exit;
        }
        include 'views/student/attendance/index.php';
    }

    public function results() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            header('Location: /login');
            exit;
        }
        include 'views/student/results/index.php';
    }

    public function fees() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            header('Location: /login');
            exit;
        }
        include 'views/student/fees/index.php';
    }

    public function profile() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            header('Location: /login');
            exit;
        }
        include 'views/student/profile/index.php';
    }

    // API methods
    public function getDashboardStats() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student' || !$this->student_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stats = [];

        try {
            // Personal attendance percentage (last 30 days)
            $stmt = $this->db->prepare("
                SELECT AVG(CASE WHEN status = 'present' THEN 1 ELSE 0 END) * 100 as rate
                FROM attendance
                WHERE student_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$this->student_id]);
            $stats['attendance_percentage'] = round($stmt->fetch(PDO::FETCH_ASSOC)['rate'] ?? 0, 2);

            // Recent exam results (last 5)
            $stmt = $this->db->prepare("
                SELECT r.marks_obtained, r.grade, e.title, e.exam_date
                FROM results r
                JOIN exams e ON r.exam_id = e.id
                WHERE r.student_id = ?
                ORDER BY e.exam_date DESC
                LIMIT 5
            ");
            $stmt->execute([$this->student_id]);
            $stats['recent_results'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fee status
            $stmt = $this->db->prepare("
                SELECT SUM(amount) as total_fees, SUM(paid_amount) as paid_fees
                FROM fees
                WHERE student_id = ?
            ");
            $stmt->execute([$this->student_id]);
            $fee_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['fee_status'] = [
                'total' => $fee_data['total_fees'] ?? 0,
                'paid' => $fee_data['paid_fees'] ?? 0,
                'outstanding' => ($fee_data['total_fees'] ?? 0) - ($fee_data['paid_fees'] ?? 0)
            ];

            // Announcements (assuming there's an announcements table, if not, empty)
            $stmt = $this->db->prepare("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
            $stmt->execute();
            $stats['announcements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            $stats = array_fill_keys(['attendance_percentage', 'recent_results', 'fee_status', 'announcements'], []);
        }

        echo json_encode($stats);
    }

    public function getAttendanceData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student' || !$this->student_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $month = $_GET['month'] ?? date('Y-m');

        try {
            $stmt = $this->db->prepare("
                SELECT date, status
                FROM attendance
                WHERE student_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
                ORDER BY date DESC
            ");
            $stmt->execute([$this->student_id, $month]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getResultsData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student' || !$this->student_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT r.marks_obtained, r.grade, r.remarks, e.title, e.exam_date, e.total_marks, sub.name as subject_name
                FROM results r
                JOIN exams e ON r.exam_id = e.id
                JOIN subjects sub ON e.subject_id = sub.id
                WHERE r.student_id = ?
                ORDER BY e.exam_date DESC
            ");
            $stmt->execute([$this->student_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getFeesData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student' || !$this->student_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT f.*, p.payment_date, p.amount as paid_amount, p.receipt_number
                FROM fees f
                LEFT JOIN fee_payments p ON f.id = p.fee_id
                WHERE f.student_id = ?
                ORDER BY f.due_date DESC
            ");
            $stmt->execute([$this->student_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getProfile() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student' || !$this->student_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT s.*, c.name as class_name, u.email
                FROM students s
                JOIN classes c ON s.class_id = c.id
                JOIN users u ON s.user_id = u.id
                WHERE s.id = ?
            ");
            $stmt->execute([$this->student_id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode($profile);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>