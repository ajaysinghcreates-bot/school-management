<?php
class TeacherController {
    private $db;
    private $teacher_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $session = Session::getInstance();

        // Get teacher ID from session user_id (assuming users.id = teachers.user_id)
        if ($session->isLoggedIn()) {
            $stmt = $this->db->prepare("SELECT id FROM teachers WHERE user_id = ?");
            $stmt->execute([$session->getUserId()]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->teacher_id = $teacher ? $teacher['id'] : null;
        }
    }

    public function dashboard() {
        $auth = new Auth();
        $auth->permissionCheck('teacher.access');

        include 'views/teacher/dashboard/index.php';
    }

    public function attendance() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher') {
            header('Location: /login');
            exit;
        }
        include 'views/teacher/attendance/index.php';
    }

    public function classes() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher') {
            header('Location: /login');
            exit;
        }
        include 'views/teacher/classes/index.php';
    }

    public function exams() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher') {
            header('Location: /login');
            exit;
        }
        include 'views/teacher/exams/index.php';
    }

    public function profile() {
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher') {
            header('Location: /login');
            exit;
        }
        include 'views/teacher/profile/index.php';
    }

    // API methods
    public function getDashboardStats() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('teacher.access');
        if (!$this->teacher_id) {
            echo json_encode(['error' => 'Teacher profile not found']);
            return;
        }

        $stats = [];

        try {
            // Classes assigned
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT c.id) as total FROM classes c JOIN subjects s ON c.id = s.class_id WHERE s.teacher_id = ?");
            $stmt->execute([$this->teacher_id]);
            $stats['total_classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Subjects assigned
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM subjects WHERE teacher_id = ?");
            $stmt->execute([$this->teacher_id]);
            $stats['total_subjects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Students in assigned classes
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT s.id) as total FROM students s JOIN subjects sub ON s.class_id = sub.class_id WHERE sub.teacher_id = ?");
            $stmt->execute([$this->teacher_id]);
            $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Attendance rate for last 30 days in assigned classes
            $stmt = $this->db->prepare("
                SELECT AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100 as rate
                FROM attendance a
                JOIN students s ON a.student_id = s.id
                JOIN subjects sub ON s.class_id = sub.class_id
                WHERE sub.teacher_id = ? AND a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$this->teacher_id]);
            $stats['attendance_rate'] = round($stmt->fetch(PDO::FETCH_ASSOC)['rate'] ?? 0, 2);

            // Upcoming exams
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM exams WHERE subject_id IN (SELECT id FROM subjects WHERE teacher_id = ?) AND exam_date >= CURDATE()");
            $stmt->execute([$this->teacher_id]);
            $stats['upcoming_exams'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Average performance (from results)
            $stmt = $this->db->prepare("SELECT AVG(marks_obtained / total_marks * 100) as avg FROM results r JOIN exams e ON r.exam_id = e.id WHERE e.subject_id IN (SELECT id FROM subjects WHERE teacher_id = ?)");
            $stmt->execute([$this->teacher_id]);
            $stats['avg_performance'] = round($stmt->fetch(PDO::FETCH_ASSOC)['avg'] ?? 0, 2);

        } catch (Exception $e) {
            $stats = array_fill_keys(['total_classes', 'total_subjects', 'total_students', 'attendance_rate', 'upcoming_exams', 'avg_performance'], 0);
        }

        echo json_encode($stats);
    }

    public function getAttendanceData() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $date = $_GET['date'] ?? date('Y-m-d');
        $class_id = $_GET['class_id'] ?? '';

        try {
            $where = ["sub.teacher_id = ?"];
            $params = [$this->teacher_id];
            if ($class_id) {
                $where[] = "s.class_id = ?";
                $params[] = $class_id;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);

            $stmt = $this->db->prepare("
                SELECT s.id, s.name, c.name as class_name, COALESCE(a.status, 'not_marked') as status
                FROM students s
                JOIN classes c ON s.class_id = c.id
                JOIN subjects sub ON c.id = sub.class_id
                LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
                $whereClause
                GROUP BY s.id
                ORDER BY s.name
            ");
            $stmt->execute(array_merge([$date], $params));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createAttendance() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $attendances = $data['attendances'] ?? [];

        try {
            $this->db->beginTransaction();
            foreach ($attendances as $att) {
                // Check if teacher is assigned to the student's class
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count FROM subjects sub
                    JOIN students s ON sub.class_id = s.class_id
                    WHERE sub.teacher_id = ? AND s.id = ?
                ");
                $stmt->execute([$this->teacher_id, $att['student_id']]);
                if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) continue;

                // Insert or update
                $stmt = $this->db->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
                $stmt->execute([$att['student_id'], $data['date'], $att['status'], $att['status']]);
            }
            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateAttendance() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            // Check if teacher can update this attendance
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM attendance a
                JOIN students s ON a.student_id = s.id
                JOIN subjects sub ON s.class_id = sub.class_id
                WHERE a.id = ? AND sub.teacher_id = ?
            ");
            $stmt->execute([$id, $this->teacher_id]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }

            $stmt = $this->db->prepare("UPDATE attendance SET status=? WHERE id=?");
            $stmt->execute([$data['status'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getClassesData() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT c.id, c.name, c.description, GROUP_CONCAT(sub.name SEPARATOR ', ') as subjects
                FROM classes c
                JOIN subjects sub ON c.id = sub.class_id
                WHERE sub.teacher_id = ?
                GROUP BY c.id
                ORDER BY c.name
            ");
            $stmt->execute([$this->teacher_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getExamsData() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT e.*, sub.name as subject_name, c.name as class_name
                FROM exams e
                JOIN subjects sub ON e.subject_id = sub.id
                JOIN classes c ON sub.class_id = c.id
                WHERE sub.teacher_id = ?
                ORDER BY e.exam_date DESC
            ");
            $stmt->execute([$this->teacher_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createExam() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Check if subject belongs to teacher
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM subjects WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$data['subject_id'], $this->teacher_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO exams (title, subject_id, exam_date, total_marks, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['title'], $data['subject_id'], $data['exam_date'], $data['total_marks'], $data['description']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateExam() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if exam belongs to teacher's subject
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM exams e
            JOIN subjects sub ON e.subject_id = sub.id
            WHERE e.id = ? AND sub.teacher_id = ?
        ");
        $stmt->execute([$id, $this->teacher_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("UPDATE exams SET title=?, subject_id=?, exam_date=?, total_marks=?, description=? WHERE id=?");
            $stmt->execute([$data['title'], $data['subject_id'], $data['exam_date'], $data['total_marks'], $data['description'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getResultsData() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $exam_id = $_GET['exam_id'] ?? '';

        try {
            $where = ["sub.teacher_id = ?"];
            $params = [$this->teacher_id];
            if ($exam_id) {
                $where[] = "r.exam_id = ?";
                $params[] = $exam_id;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);

            $stmt = $this->db->prepare("
                SELECT r.*, s.name as student_name, e.title as exam_title, sub.name as subject_name
                FROM results r
                JOIN students s ON r.student_id = s.id
                JOIN exams e ON r.exam_id = e.id
                JOIN subjects sub ON e.subject_id = sub.id
                $whereClause
                ORDER BY r.created_at DESC
            ");
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createResult() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Check if exam belongs to teacher's subject
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM exams e
            JOIN subjects sub ON e.subject_id = sub.id
            WHERE e.id = ? AND sub.teacher_id = ?
        ");
        $stmt->execute([$data['exam_id'], $this->teacher_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO results (exam_id, student_id, marks_obtained, grade, remarks) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['exam_id'], $data['student_id'], $data['marks_obtained'], $data['grade'], $data['remarks']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateResult() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if result belongs to teacher's exam
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM results r
            JOIN exams e ON r.exam_id = e.id
            JOIN subjects sub ON e.subject_id = sub.id
            WHERE r.id = ? AND sub.teacher_id = ?
        ");
        $stmt->execute([$id, $this->teacher_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("UPDATE results SET marks_obtained=?, grade=?, remarks=? WHERE id=?");
            $stmt->execute([$data['marks_obtained'], $data['grade'], $data['remarks'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getProfile() {
        header('Content-Type: application/json');
        $session = Session::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 'teacher' || !$this->teacher_id) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$this->teacher_id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get assigned subjects
            $stmt = $this->db->prepare("
                SELECT sub.name, c.name as class_name
                FROM subjects sub
                JOIN classes c ON sub.class_id = c.id
                WHERE sub.teacher_id = ?
            ");
            $stmt->execute([$this->teacher_id]);
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $profile['subjects'] = $subjects;

            echo json_encode($profile);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>