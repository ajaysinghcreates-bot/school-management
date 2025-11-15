<?php
class ParentController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        Session::getInstance(); // Initialize session
    }

    public function dashboard() {
        $auth = new Auth();
        $auth->permissionCheck('parent.access');

        $session = Session::getInstance();

        // Get parent's children
        $children = $this->getChildren($session->getUserId());

        // Get recent notifications
        $notifications = $this->getNotifications($session->getUserId());

        include 'views/parent/dashboard/index.php';
    }

    public function children() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $children = $this->getChildren($session->getUserId());
        include 'views/parent/children/index.php';
    }

    public function attendance() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $childId = $_GET['child_id'] ?? null;
        $month = $_GET['month'] ?? date('Y-m');

        if ($childId) {
            $attendance = $this->getChildAttendance($childId, $month);
            $child = $this->getChildInfo($childId);
        }

        include 'views/parent/attendance/index.php';
    }

    public function results() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $childId = $_GET['child_id'] ?? null;

        if ($childId) {
            $results = $this->getChildResults($childId);
            $child = $this->getChildInfo($childId);
        }

        include 'views/parent/results/index.php';
    }

    public function fees() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $childId = $_GET['child_id'] ?? null;

        if ($childId) {
            $fees = $this->getChildFees($childId);
            $child = $this->getChildInfo($childId);
        }

        include 'views/parent/fees/index.php';
    }

    public function events() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $events = $this->getUpcomingEvents();
        include 'views/parent/events/index.php';
    }

    public function profile() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'parent') {
            header('Location: /login');
            exit;
        }

        $profile = $this->getProfile($session->getUserId());
        include 'views/parent/profile/index.php';
    }

    // API Methods
    public function getDashboardStats() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('parent.access');

        $userId = $session->getUserId();
        $children = $this->getChildren($userId);

        $stats = [
            'total_children' => count($children),
            'pending_fees' => 0,
            'upcoming_events' => count($this->getUpcomingEvents()),
            'unread_notifications' => count($this->getUnreadNotifications($userId))
        ];

        // Calculate pending fees
        foreach ($children as $child) {
            $fees = $this->getChildFees($child['id']);
            foreach ($fees as $fee) {
                if ($fee['status'] === 'pending') {
                    $stats['pending_fees'] += $fee['amount'];
                }
            }
        }

        echo json_encode($stats);
    }

    // Helper methods
    private function getChildren($userId) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.guardian_email = (SELECT email FROM users WHERE id = ?)
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getChildInfo($childId) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?
        ");
        $stmt->execute([$childId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getChildAttendance($childId, $month) {
        $stmt = $this->db->prepare("
            SELECT a.*, DATE_FORMAT(a.date, '%d/%m/%Y') as formatted_date
            FROM attendance a
            WHERE a.student_id = ? AND DATE_FORMAT(a.date, '%Y-%m') = ?
            ORDER BY a.date DESC
        ");
        $stmt->execute([$childId, $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getChildResults($childId) {
        $stmt = $this->db->prepare("
            SELECT er.*, e.title as exam_title, s.name as subject_name,
                   e.total_marks, e.exam_date
            FROM exam_results er
            JOIN exams e ON er.exam_id = e.id
            JOIN subjects s ON e.subject_id = s.id
            WHERE er.student_id = ?
            ORDER BY e.exam_date DESC
        ");
        $stmt->execute([$childId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getChildFees($childId) {
        $stmt = $this->db->prepare("
            SELECT sf.*, fs.fee_type, fs.frequency
            FROM student_fees sf
            JOIN fee_structures fs ON sf.fee_structure_id = fs.id
            WHERE sf.student_id = ?
            ORDER BY sf.due_date DESC
        ");
        $stmt->execute([$childId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUpcomingEvents() {
        $stmt = $this->db->query("
            SELECT * FROM events
            WHERE event_date >= CURDATE() AND status = 'upcoming'
            ORDER BY event_date ASC
            LIMIT 10
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getNotifications($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = ? OR user_id IS NULL
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUnreadNotifications($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM notifications
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = FALSE
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getProfile($userId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}