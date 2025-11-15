<?php
class User {
    protected $db;
    protected $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $data['password'] = Security::hashPassword($data['password']);
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

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && Security::verifyPassword($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function updateLastLogin($id) {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }

    public function getAll($conditions = [], $orderBy = 'created_at DESC', $limit = null) {
        $where = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderBy}";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function count($conditions = []) {
        $where = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $sql = "SELECT COUNT(*) as count FROM {$this->table} {$where}";
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
}