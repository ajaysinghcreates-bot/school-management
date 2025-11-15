<?php
class Auth {
    protected $session;
    protected $permission;

    public function __construct() {
        $this->session = Session::getInstance();
        $this->permission = new Permission();
    }

    public function handle($requiredRole = null) {
        if (!$this->session->isLoggedIn()) {
            $this->redirectToLogin();
        }

        if ($requiredRole && !$this->session->checkRole($requiredRole)) {
            $this->handleUnauthorized();
        }
    }

    public function guestOnly() {
        if ($this->session->isLoggedIn()) {
            $this->redirectToDashboard();
        }
    }

    public function apiAuth() {
        if (!$this->session->isLoggedIn()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function roleCheck($role) {
        if (!$this->session->checkRole($role)) {
            $this->handleUnauthorized();
        }
    }

    public function permissionCheck($permission) {
        if (!$this->session->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userRole = $this->session->getUserRole();
        if (!$this->permission->hasPermission($userRole, $permission)) {
            $this->handleUnauthorized();
        }
    }

    public function resourceAccessCheck($resource, $action = 'view', $resourceOwner = null) {
        if (!$this->session->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userRole = $this->session->getUserRole();
        if (!$this->permission->canAccessResource($userRole, $resource, $action, $resourceOwner)) {
            $this->handleUnauthorized();
        }
    }

    public function can($permission) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }

        $userRole = $this->session->getUserRole();
        return $this->permission->hasPermission($userRole, $permission);
    }

    public function canAny($permissions) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }

        $userRole = $this->session->getUserRole();
        return $this->permission->hasAnyPermission($userRole, $permissions);
    }

    public function canAll($permissions) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }

        $userRole = $this->session->getUserRole();
        return $this->permission->hasAllPermissions($userRole, $permissions);
    }

    protected function redirectToLogin() {
        header('Location: /login');
        exit;
    }

    protected function redirectToDashboard() {
        $role = $this->session->getUserRole();
        $redirects = [
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'cashier' => '/cashier/dashboard',
            'student' => '/student/dashboard',
            'parent' => '/parent/dashboard'
        ];

        $redirect = $redirects[$role] ?? '/';
        header('Location: ' . $redirect);
        exit;
    }

    protected function handleUnauthorized() {
        http_response_code(403);

        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
        } else {
            echo "403 Forbidden: You don't have permission to access this resource.";
        }
        exit;
    }

    protected function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0;
    }
}