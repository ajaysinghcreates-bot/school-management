<?php
class Student {
    protected $db;
    protected $table = 'students';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name, c.section
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByScholarNumber($scholarNumber) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE scholar_number = ?");
        $stmt->execute([$scholarNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function getAll($filters = [], $orderBy = 's.created_at DESC', $limit = null, $offset = null) {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $where[] = "(s.name LIKE ? OR s.email LIKE ? OR s.phone LIKE ? OR s.scholar_number LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }

        if (!empty($filters['class_id'])) {
            $where[] = "s.class_id = ?";
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "s.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT s.*, c.name as class_name, c.section
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            {$whereClause}
            ORDER BY {$orderBy}
        ";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function count($filters = []) {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR scholar_number LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }

        if (!empty($filters['class_id'])) {
            $where[] = "class_id = ?";
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as count FROM {$this->table} {$whereClause}";
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }

    public function getByClass($classId) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as class_name
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.roll_number, s.name
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceStats($studentId, $month = null) {
        $where = "a.student_id = ?";
        $params = [$studentId];

        if ($month) {
            $where .= " AND DATE_FORMAT(a.date, '%Y-%m') = ?";
            $params[] = $month;
        }

        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days
            FROM attendance a
            WHERE {$where}
        ");
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFeeSummary($studentId) {
        $stmt = $this->db->prepare("
            SELECT
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount,
                COUNT(*) as total_fees
            FROM student_fees
            WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function promote($studentId, $newClassId, $academicYear) {
        // Create promotion record
        $promotionData = [
            'student_id' => $studentId,
            'from_class_id' => $this->find($studentId)['class_id'],
            'to_class_id' => $newClassId,
            'academic_year' => $academicYear,
            'promotion_date' => date('Y-m-d'),
            'promoted_by' => Session::getInstance()->getUserId(),
            'status' => 'promoted'
        ];

        $this->db->insert('student_promotions', $promotionData);

        // Update student class
        return $this->update($studentId, ['class_id' => $newClassId]);
    }
}