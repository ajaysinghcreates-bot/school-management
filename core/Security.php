<?php
class Security {
    private static $csrfTokens = [];

    public static function generateCSRFToken() {
        $config = require 'config/security.php';
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes($config['csrf']['token_length']));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        $config = require 'config/security.php';
        if (!$config['csrf']['enabled']) {
            return true;
        }

        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }

        if ($config['csrf']['regenerate']) {
            self::generateCSRFToken();
        }

        return true;
    }

    public static function sanitizeInput($input) {
        $config = require 'config/security.php';
        if (!$config['validation']['sanitize_input']) {
            return $input;
        }

        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }

        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    public static function sanitizeHTML($input) {
        $config = require 'config/security.php';
        if (!$config['xss']['enabled']) {
            return $input;
        }

        // Strip all tags except allowed ones
        $allowedTags = implode('', $config['xss']['allowed_tags']);
        return strip_tags($input, $allowedTags);
    }

    public static function hashPassword($password) {
        $config = require 'config/security.php';
        return password_hash($password, $config['password']['hash_algorithm'], [
            'cost' => $config['password']['cost']
        ]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function validatePassword($password) {
        $config = require 'config/security.php';

        if (strlen($password) < $config['password']['min_length']) {
            return false;
        }

        if ($config['password']['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if ($config['password']['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            return false;
        }

        if ($config['password']['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            return false;
        }

        if ($config['password']['require_symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    public static function encrypt($data, $key = null) {
        $config = require 'config/security.php';
        $key = $key ?: $config['encryption']['key'];

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($config['encryption']['cipher']));
        $encrypted = openssl_encrypt($data, $config['encryption']['cipher'], $key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data, $key = null) {
        $config = require 'config/security.php';
        $key = $key ?: $config['encryption']['key'];

        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($config['encryption']['cipher']);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        return openssl_decrypt($encrypted, $config['encryption']['cipher'], $key, 0, $iv);
    }

    public static function generateRandomToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function rateLimitCheck($identifier, $maxAttempts = 5, $decayMinutes = 1) {
        $config = require 'config/security.php';
        if (!$config['rate_limit']['enabled']) {
            return true;
        }

        $key = 'rate_limit_' . $identifier;
        $attempts = $_SESSION[$key]['attempts'] ?? 0;
        $lastAttempt = $_SESSION[$key]['last_attempt'] ?? 0;

        $now = time();
        $decaySeconds = $decayMinutes * 60;

        // Reset if decay time has passed
        if ($now - $lastAttempt > $decaySeconds) {
            $attempts = 0;
        }

        if ($attempts >= $maxAttempts) {
            return false;
        }

        $_SESSION[$key] = [
            'attempts' => $attempts + 1,
            'last_attempt' => $now
        ];

        return true;
    }

    public static function setSecurityHeaders() {
        $config = require 'config/security.php';

        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');

        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');

        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');

        // HSTS
        if ($config['headers']['hsts']['enabled']) {
            $hsts = 'max-age=' . $config['headers']['hsts']['max_age'];
            if ($config['headers']['hsts']['include_subdomains']) {
                $hsts .= '; includeSubDomains';
            }
            header('Strict-Transport-Security: ' . $hsts);
        }

        // CSP
        if ($config['headers']['csp']['enabled']) {
            header('Content-Security-Policy: ' . $config['headers']['csp']['policy']);
        }
    }
}