<?php
class Permission {
    protected $db;
    protected $table = 'user_roles';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getRolePermissions($roleName) {
        $stmt = $this->db->prepare("SELECT permissions FROM {$this->table} WHERE role_name = ?");
        $stmt->execute([$roleName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['permissions']) {
            return json_decode($result['permissions'], true);
        }

        return [];
    }

    public function hasPermission($roleName, $permission) {
        $permissions = $this->getRolePermissions($roleName);

        // Check for wildcard permission (all permissions)
        if (isset($permissions['all']) && $permissions['all'] === true) {
            return true;
        }

        // Check for specific permission
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }

    public function hasAnyPermission($roleName, $permissions) {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($roleName, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions($roleName, $permissions) {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($roleName, $permission)) {
                return false;
            }
        }
        return true;
    }

    public function updateRolePermissions($roleName, $permissions) {
        $permissionsJson = json_encode($permissions);

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET permissions = ?
            WHERE role_name = ?
        ");
        return $stmt->execute([$permissionsJson, $roleName]);
    }

    public function getAllRoles() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY role_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole($roleName, $permissions = []) {
        $permissionsJson = json_encode($permissions);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (role_name, permissions)
            VALUES (?, ?)
        ");
        return $stmt->execute([$roleName, $permissionsJson]);
    }

    public function deleteRole($roleName) {
        // Don't allow deletion of system roles
        $systemRoles = ['admin', 'teacher', 'student', 'cashier', 'parent'];
        if (in_array($roleName, $systemRoles)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE role_name = ?");
        return $stmt->execute([$roleName]);
    }

    // Predefined permission groups
    public static function getDefaultPermissions() {
        return [
            'admin' => [
                'all' => true, // Admin has all permissions
            ],
            'teacher' => [
                // Student management (limited)
                'students.view' => true,
                'students.view_own_class' => true,

                // Attendance management
                'attendance.view' => true,
                'attendance.mark' => true,
                'attendance.edit_own' => true,

                // Exam management
                'exams.view' => true,
                'exams.create' => true,
                'exams.edit_own' => true,
                'exams.delete_own' => true,

                // Results management
                'results.view' => true,
                'results.enter' => true,
                'results.edit_own' => true,

                // Class management
                'classes.view_assigned' => true,

                // Profile management
                'profile.view' => true,
                'profile.edit_own' => true,
            ],
            'cashier' => [
                // Fee management
                'fees.view' => true,
                'fees.collect' => true,
                'fees.edit' => true,

                // Payment processing
                'payments.process' => true,
                'payments.view' => true,

                // Financial reports
                'reports.financial' => true,
                'reports.fees' => true,

                // Expense management
                'expenses.view' => true,
                'expenses.create' => true,
                'expenses.edit' => true,

                // Document generation
                'documents.generate' => true,
                'documents.print' => true,

                // Profile management
                'profile.view' => true,
                'profile.edit_own' => true,
            ],
            'student' => [
                // Personal data
                'profile.view' => true,
                'profile.edit_own' => true,

                // Academic records
                'attendance.view_own' => true,
                'results.view_own' => true,
                'fees.view_own' => true,

                // School content
                'events.view' => true,
                'gallery.view' => true,
                'announcements.view' => true,
            ],
            'parent' => [
                // Children's data
                'children.view' => true,
                'children.attendance' => true,
                'children.results' => true,
                'children.fees' => true,

                // School content
                'events.view' => true,
                'gallery.view' => true,
                'announcements.view' => true,

                // Profile management
                'profile.view' => true,
                'profile.edit_own' => true,
            ],
        ];
    }

    // Check if user can access a specific resource
    public function canAccessResource($userRole, $resource, $action = 'view', $resourceOwner = null) {
        $permissions = $this->getRolePermissions($userRole);

        // Admin has access to everything
        if (isset($permissions['all']) && $permissions['all'] === true) {
            return true;
        }

        $permissionKey = $resource . '.' . $action;

        // Check specific permission
        if (isset($permissions[$permissionKey]) && $permissions[$permissionKey] === true) {
            return true;
        }

        // Check ownership-based permissions
        if ($resourceOwner && Session::getInstance()->getUserId() == $resourceOwner) {
            $ownerPermissionKey = $resource . '.edit_own';
            if (isset($permissions[$ownerPermissionKey]) && $permissions[$ownerPermissionKey] === true) {
                return true;
            }
        }

        return false;
    }

    // Initialize default permissions in database
    public function initializeDefaultPermissions() {
        $defaultPermissions = self::getDefaultPermissions();

        foreach ($defaultPermissions as $roleName => $permissions) {
            $this->updateRolePermissions($roleName, $permissions);
        }
    }
}