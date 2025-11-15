<?php
class Session {
    private static $instance = null;
    private $config;

    private function __construct() {
        $this->config = require 'config/app.php';
        $this->startSession();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session configuration
            ini_set('session.gc_maxlifetime', $this->config['session']['lifetime']);
            ini_set('session.cookie_lifetime', $this->config['session']['lifetime']);
            ini_set('session.cookie_httponly', $this->config['session']['httponly']);
            ini_set('session.cookie_secure', $this->config['session']['secure']);

            session_name($this->config['session']['name']);
            session_set_cookie_params(
                $this->config['session']['lifetime'],
                $this->config['session']['path'],
                $this->config['session']['domain'],
                $this->config['session']['secure'],
                $this->config['session']['httponly']
            );

            session_start();
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        session_destroy();
        $_SESSION = [];
    }

    public function regenerateId($deleteOldSession = true) {
        return session_regenerate_id($deleteOldSession);
    }

    public function getId() {
        return session_id();
    }

    public function getUserId() {
        return $this->get('user_id');
    }

    public function getUserRole() {
        return $this->get('user_role');
    }

    public function isLoggedIn() {
        return $this->has('user_id') && $this->has('user_role');
    }

    public function login($userId, $userRole, $userData = []) {
        $this->set('user_id', $userId);
        $this->set('user_role', $userRole);
        $this->set('user_data', $userData);
        $this->set('login_time', time());
        $this->regenerateId();
    }

    public function logout() {
        $this->remove('user_id');
        $this->remove('user_role');
        $this->remove('user_data');
        $this->remove('login_time');
        $this->regenerateId();
        $this->destroy();
    }

    public function checkRole($requiredRole) {
        $userRole = $this->getUserRole();
        if (!$userRole) {
            return false;
        }

        $roleHierarchy = [
            'admin' => 4,
            'teacher' => 3,
            'cashier' => 2,
            'student' => 1,
            'parent' => 1
        ];

        return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
    }

    public function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash($key, $default = null) {
        $value = $_SESSION['flash'][$key] ?? $default;
        if (isset($_SESSION['flash'][$key])) {
            unset($_SESSION['flash'][$key]);
        }
        return $value;
    }

    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }

    public function getAllFlashes() {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    public function setTempData($key, $value, $ttl = 300) {
        $_SESSION['temp_data'][$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }

    public function getTempData($key, $default = null) {
        if (!isset($_SESSION['temp_data'][$key])) {
            return $default;
        }

        $data = $_SESSION['temp_data'][$key];
        if (time() > $data['expires']) {
            unset($_SESSION['temp_data'][$key]);
            return $default;
        }

        return $data['value'];
    }

    public function cleanupExpiredTempData() {
        if (!isset($_SESSION['temp_data'])) {
            return;
        }

        foreach ($_SESSION['temp_data'] as $key => $data) {
            if (time() > $data['expires']) {
                unset($_SESSION['temp_data'][$key]);
            }
        }
    }
}