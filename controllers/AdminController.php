<?php
class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        Session::getInstance(); // Initialize session
    }

    public function dashboard() {
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        // Fetch key statistics
        $stats = $this->getDashboardStats();

        // Pass data to view
        include 'views/admin/dashboard/index.php';
    }

    private function getDashboardStats() {
        $stats = [];

        try {
            // Total students
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM students");
            $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total teachers
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM teachers");
            $stats['total_teachers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total classes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM classes");
            $stats['total_classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Attendance rate (last 30 days)
            $stmt = $this->db->prepare("
                SELECT AVG(CASE WHEN status = 'present' THEN 1 ELSE 0 END) * 100 as rate
                FROM attendance
                WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
            $stats['attendance_rate'] = round($stmt->fetch(PDO::FETCH_ASSOC)['rate'], 2);

            // Other stats as needed
            $stats['total_events'] = $this->db->query("SELECT COUNT(*) as total FROM events")->fetch(PDO::FETCH_ASSOC)['total'];
            $stats['total_fees_pending'] = $this->db->query("SELECT SUM(amount) as total FROM fees WHERE status = 'pending'")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        } catch (Exception $e) {
            // Handle errors, perhaps log or set defaults
            $stats = array_fill_keys(['total_students', 'total_teachers', 'total_classes', 'attendance_rate', 'total_events', 'total_fees_pending'], 0);
        }

        return $stats;
    }

    // API method for dynamic stats
    public function getStats() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stats = $this->getDashboardStats();
        echo json_encode($stats);
    }

    // API for chart data, e.g., monthly enrollment
    public function getChartData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        // Example: Monthly student enrollment for last 12 months
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
            $stmt->execute([$month]);
            $data[] = [
                'month' => date('M Y', strtotime($month)),
                'count' => (int)$stmt->fetch(PDO::FETCH_ASSOC)['count']
            ];
        }
        echo json_encode($data);
    }

    // API for attendance trend
    public function getAttendanceTrend() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                FROM attendance WHERE date = ?
            ");
            $stmt->execute([$date]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $rate = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 2) : 0;
            $data[] = [
                'date' => $date,
                'rate' => $rate
            ];
        }
        echo json_encode($data);
    }

    // Recent notifications (assume notifications table)
    public function getNotifications() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($notifications);
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    // Students Management
    public function students() {
        $auth = new Auth();
        $auth->permissionCheck('students.view');
        include 'views/admin/students/index.php';
    }

    // Teachers Management
    public function teachers() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/teachers/index.php';
    }

    // Classes & Subjects Management
    public function classes() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/classes/index.php';
    }

    // Attendance Management
    public function attendance() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/attendance/index.php';
    }

    // Exams & Results Management
    public function exams() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/exams/index.php';
    }

    // Fees Management
    public function fees() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/fees/index.php';
    }

    // Events & Announcements Management
    public function events() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/events/index.php';
    }

    // Gallery Management
    public function gallery() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/gallery/index.php';
    }

    // Reports Management
    public function reports() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        include 'views/admin/reports/index.php';
    }

    // Homepage Content Management
    public function homepage() {
        $auth = new Auth();
        $auth->permissionCheck('admin.access');
        include 'views/admin/homepage/index.php';
    }

    // Settings Management
    public function settings() {
        $auth = new Auth();
        $auth->permissionCheck('admin.access');
        include 'views/admin/settings/index.php';
    }

    // API Endpoints for Students
    public function getStudentsData() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('students.view');

        $search = $_GET['search'] ?? '';
        $class_id = $_GET['class_id'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            }
            if ($class_id) {
                $where[] = "class_id = ?";
                $params[] = $class_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM students $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT s.*, c.name as class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id $whereClause ORDER BY s.created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $students, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createStudent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO students (
                name, email, phone, address, dob, gender, class_id, enrollment_date,
                father_name, mother_name, guardian_name, guardian_phone, guardian_relation,
                blood_group, allergies, medical_conditions, emergency_contact_name, emergency_contact_phone,
                previous_school, admission_number, roll_number, category, religion, nationality,
                permanent_address, correspondence_address, aadhar_number, bank_account_number, ifsc_code
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'], $data['email'], $data['phone'], $data['address'], $data['dob'], $data['gender'],
                $data['class_id'], $data['enrollment_date'], $data['father_name'], $data['mother_name'],
                $data['guardian_name'], $data['guardian_phone'], $data['guardian_relation'], $data['blood_group'],
                $data['allergies'], $data['medical_conditions'], $data['emergency_contact_name'],
                $data['emergency_contact_phone'], $data['previous_school'], $data['admission_number'],
                $data['roll_number'], $data['category'], $data['religion'], $data['nationality'],
                $data['permanent_address'], $data['correspondence_address'], $data['aadhar_number'],
                $data['bank_account_number'], $data['ifsc_code']
            ]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateStudent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE students SET
                name=?, email=?, phone=?, address=?, dob=?, gender=?, class_id=?,
                father_name=?, mother_name=?, guardian_name=?, guardian_phone=?, guardian_relation=?,
                blood_group=?, allergies=?, medical_conditions=?, emergency_contact_name=?, emergency_contact_phone=?,
                previous_school=?, admission_number=?, roll_number=?, category=?, religion=?, nationality=?,
                permanent_address=?, correspondence_address=?, aadhar_number=?, bank_account_number=?, ifsc_code=?
                WHERE id=?");
            $stmt->execute([
                $data['name'], $data['email'], $data['phone'], $data['address'], $data['dob'], $data['gender'], $data['class_id'],
                $data['father_name'], $data['mother_name'], $data['guardian_name'], $data['guardian_phone'], $data['guardian_relation'],
                $data['blood_group'], $data['allergies'], $data['medical_conditions'], $data['emergency_contact_name'],
                $data['emergency_contact_phone'], $data['previous_school'], $data['admission_number'], $data['roll_number'],
                $data['category'], $data['religion'], $data['nationality'], $data['permanent_address'], $data['correspondence_address'],
                $data['aadhar_number'], $data['bank_account_number'], $data['ifsc_code'], $id
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteStudent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM students WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function bulkImportStudents() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $students = json_decode(file_get_contents('php://input'), true);
        $success = 0;
        $errors = [];
        foreach ($students as $student) {
            try {
                $stmt = $this->db->prepare("INSERT INTO students (
                    name, email, phone, address, dob, gender, class_id, enrollment_date,
                    father_name, mother_name, guardian_name, guardian_phone, guardian_relation,
                    blood_group, allergies, medical_conditions, emergency_contact_name, emergency_contact_phone,
                    previous_school, admission_number, roll_number, category, religion, nationality,
                    permanent_address, correspondence_address, aadhar_number, bank_account_number, ifsc_code
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $student['name'], $student['email'], $student['phone'], $student['address'], $student['dob'], $student['gender'],
                    $student['class_id'], $student['enrollment_date'], $student['father_name'] ?? null, $student['mother_name'] ?? null,
                    $student['guardian_name'] ?? null, $student['guardian_phone'] ?? null, $student['guardian_relation'] ?? null,
                    $student['blood_group'] ?? null, $student['allergies'] ?? null, $student['medical_conditions'] ?? null,
                    $student['emergency_contact_name'] ?? null, $student['emergency_contact_phone'] ?? null,
                    $student['previous_school'] ?? null, $student['admission_number'] ?? null, $student['roll_number'] ?? null,
                    $student['category'] ?? null, $student['religion'] ?? null, $student['nationality'] ?? null,
                    $student['permanent_address'] ?? null, $student['correspondence_address'] ?? null,
                    $student['aadhar_number'] ?? null, $student['bank_account_number'] ?? null, $student['ifsc_code'] ?? null
                ]);
                $success++;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        echo json_encode(['success' => $success, 'errors' => $errors]);
    }

    // API Endpoints for Teachers
    public function getTeachersData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $subject_id = $_GET['subject_id'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            }
            if ($subject_id) {
                $where[] = "subject_id = ?";
                $params[] = $subject_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM teachers $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT t.*, s.name as subject_name FROM teachers t LEFT JOIN subjects s ON t.subject_id = s.id $whereClause ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $teachers, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createTeacher() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO teachers (
                name, email, phone, address, dob, gender, subject_id, hire_date, salary,
                employee_id, qualification, experience_years, specialization, previous_school,
                emergency_contact_name, emergency_contact_phone, blood_group, marital_status,
                bank_account_number, ifsc_code, pan_number, aadhar_number, teaching_subjects,
                monday_schedule, tuesday_schedule, wednesday_schedule, thursday_schedule,
                friday_schedule, saturday_schedule, sunday_schedule
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'], $data['email'], $data['phone'], $data['address'], $data['dob'], $data['gender'],
                $data['subject_id'], $data['hire_date'], $data['salary'], $data['employee_id'], $data['qualification'],
                $data['experience_years'], $data['specialization'], $data['previous_school'],
                $data['emergency_contact_name'], $data['emergency_contact_phone'], $data['blood_group'],
                $data['marital_status'], $data['bank_account_number'], $data['ifsc_code'], $data['pan_number'],
                $data['aadhar_number'], $data['teaching_subjects'], $data['monday_schedule'],
                $data['tuesday_schedule'], $data['wednesday_schedule'], $data['thursday_schedule'],
                $data['friday_schedule'], $data['saturday_schedule'], $data['sunday_schedule']
            ]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateTeacher() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE teachers SET
                name=?, email=?, phone=?, address=?, dob=?, gender=?, subject_id=?, salary=?,
                employee_id=?, qualification=?, experience_years=?, specialization=?, previous_school=?,
                emergency_contact_name=?, emergency_contact_phone=?, blood_group=?, marital_status=?,
                bank_account_number=?, ifsc_code=?, pan_number=?, aadhar_number=?, teaching_subjects=?,
                monday_schedule=?, tuesday_schedule=?, wednesday_schedule=?, thursday_schedule=?,
                friday_schedule=?, saturday_schedule=?, sunday_schedule=?
                WHERE id=?");
            $stmt->execute([
                $data['name'], $data['email'], $data['phone'], $data['address'], $data['dob'], $data['gender'],
                $data['subject_id'], $data['salary'], $data['employee_id'], $data['qualification'],
                $data['experience_years'], $data['specialization'], $data['previous_school'],
                $data['emergency_contact_name'], $data['emergency_contact_phone'], $data['blood_group'],
                $data['marital_status'], $data['bank_account_number'], $data['ifsc_code'], $data['pan_number'],
                $data['aadhar_number'], $data['teaching_subjects'], $data['monday_schedule'],
                $data['tuesday_schedule'], $data['wednesday_schedule'], $data['thursday_schedule'],
                $data['friday_schedule'], $data['saturday_schedule'], $data['sunday_schedule'], $id
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteTeacher() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM teachers WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Classes & Subjects
    public function getClassesData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = $search ? "WHERE name LIKE ?" : '';
            $params = $search ? ["%$search%"] : [];

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM classes $where");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT * FROM classes $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $classes, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createClass() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO classes (name, description) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['description']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateClass() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE classes SET name=?, description=? WHERE id=?");
            $stmt->execute([$data['name'], $data['description'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteClass() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM classes WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getSubjectsData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = $search ? "WHERE name LIKE ?" : '';
            $params = $search ? ["%$search%"] : [];

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM subjects $where");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT * FROM subjects $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $subjects, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createSubject() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO subjects (name, description, class_id) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['description'], $data['class_id']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateSubject() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE subjects SET name=?, description=?, class_id=? WHERE id=?");
            $stmt->execute([$data['name'], $data['description'], $data['class_id'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteSubject() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM subjects WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Attendance
    public function getAttendanceData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? '';
        $class_id = $_GET['class_id'] ?? '';
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
            if ($date) {
                $where[] = "a.date = ?";
                $params[] = $date;
            }
            if ($class_id) {
                $where[] = "s.class_id = ?";
                $params[] = $class_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM attendance a JOIN students s ON a.student_id = s.id $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT a.*, s.name as student_name, c.name as class_name FROM attendance a JOIN students s ON a.student_id = s.id LEFT JOIN classes c ON s.class_id = c.id $whereClause ORDER BY a.date DESC, a.created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $attendance, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createAttendance() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
            $stmt->execute([$data['student_id'], $data['date'], $data['status']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateAttendance() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE attendance SET status=? WHERE id=?");
            $stmt->execute([$data['status'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteAttendance() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM attendance WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Exams & Results
    public function getExamsData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $subject_id = $_GET['subject_id'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "title LIKE ?";
                $params[] = "%$search%";
            }
            if ($subject_id) {
                $where[] = "subject_id = ?";
                $params[] = $subject_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM exams $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT e.*, s.name as subject_name FROM exams e LEFT JOIN subjects s ON e.subject_id = s.id $whereClause ORDER BY e.exam_date DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $exams, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createExam() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
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
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE exams SET title=?, subject_id=?, exam_date=?, total_marks=?, description=? WHERE id=?");
            $stmt->execute([$data['title'], $data['subject_id'], $data['exam_date'], $data['total_marks'], $data['description'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteExam() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM exams WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getResultsData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $exam_id = $_GET['exam_id'] ?? '';
        $student_id = $_GET['student_id'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($exam_id) {
                $where[] = "r.exam_id = ?";
                $params[] = $exam_id;
            }
            if ($student_id) {
                $where[] = "r.student_id = ?";
                $params[] = $student_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM results r $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT r.*, s.name as student_name, e.title as exam_title, sub.name as subject_name FROM results r JOIN students s ON r.student_id = s.id JOIN exams e ON r.exam_id = e.id LEFT JOIN subjects sub ON e.subject_id = sub.id $whereClause ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $results, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createResult() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
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
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE results SET marks_obtained=?, grade=?, remarks=? WHERE id=?");
            $stmt->execute([$data['marks_obtained'], $data['grade'], $data['remarks'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteResult() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM results WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Fees
    public function getFeesData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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

    public function createFee() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO fees (student_id, amount, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['student_id'], $data['amount'], $data['description'], $data['due_date'], $data['status']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateFee() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE fees SET amount=?, description=?, due_date=?, status=? WHERE id=?");
            $stmt->execute([$data['amount'], $data['description'], $data['due_date'], $data['status'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteFee() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM fees WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function bulkUpdateFees() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['ids'];
        $status = $data['status'];
        try {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $this->db->prepare("UPDATE fees SET status=? WHERE id IN ($placeholders)");
            $stmt->execute(array_merge([$status], $ids));
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Events & Announcements
    public function getEventsData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "title LIKE ?";
                $params[] = "%$search%";
            }
            if ($type) {
                $where[] = "type = ?";
                $params[] = $type;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM events $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT * FROM events $whereClause ORDER BY event_date DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $events, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createEvent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO events (title, description, event_date, type, location) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['title'], $data['description'], $data['event_date'], $data['type'], $data['location']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateEvent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE events SET title=?, description=?, event_date=?, type=?, location=? WHERE id=?");
            $stmt->execute([$data['title'], $data['description'], $data['event_date'], $data['type'], $data['location'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteEvent() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM events WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Gallery
    public function getGalleryData() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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
                $where[] = "title LIKE ?";
                $params[] = "%$search%";
            }
            if ($category) {
                $where[] = "category = ?";
                $params[] = $category;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM gallery $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $this->db->prepare("SELECT * FROM gallery $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $gallery, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createGallery() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("INSERT INTO gallery (title, description, image_path, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['title'], $data['description'], $data['image_path'], $data['category']]);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateGallery() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $this->db->prepare("UPDATE gallery SET title=?, description=?, image_path=?, category=? WHERE id=?");
            $stmt->execute([$data['title'], $data['description'], $data['image_path'], $data['category'], $id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteGallery() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM gallery WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Homepage Content
    public function getHomepageData() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        $homepageContent = new HomepageContent();
        $section = $_GET['section'] ?? '';

        try {
            if ($section) {
                $data = $homepageContent->getSection($section);
            } else {
                $data = $homepageContent->getAllActive();
            }
            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createHomepageContent() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        $data = json_decode(file_get_contents('php://input'), true);
        $homepageContent = new HomepageContent();

        try {
            $id = $homepageContent->create($data);
            echo json_encode(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateHomepageContent() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        $id = $_GET['id'] ?? 0;
        $data = json_decode(file_get_contents('php://input'), true);
        $homepageContent = new HomepageContent();

        try {
            $homepageContent->update($id, $data);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteHomepageContent() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        $id = $_GET['id'] ?? 0;
        $homepageContent = new HomepageContent();

        try {
            $homepageContent->delete($id);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Reports
    public function getStudentReport() {
        header('Content-Type: application/json');
        $auth = new Auth();
        $auth->permissionCheck('admin.access');

        try {
            $stmt = $this->db->query("SELECT c.name as class, COUNT(s.id) as count FROM classes c LEFT JOIN students s ON c.id = s.class_id GROUP BY c.id");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getFeeReport() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->query("SELECT status, SUM(amount) as total FROM fees GROUP BY status");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // API Endpoints for Settings
    public function getSettings() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $stmt = $this->db->query("SELECT * FROM settings");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($settings);
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    public function updateSettings() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            foreach ($data as $key => $value) {
                $stmt = $this->db->prepare("UPDATE settings SET value=? WHERE key=?");
                $stmt->execute([$value, $key]);
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>