<?php
class Logger {
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';

    private static $logPath = 'logs/';

    public static function error($message, $context = []) {
        self::log(self::ERROR, $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log(self::WARNING, $message, $context);
    }

    public static function info($message, $context = []) {
        self::log(self::INFO, $message, $context);
    }

    public static function debug($message, $context = []) {
        self::log(self::DEBUG, $message, $context);
    }

    public static function access($message, $userId = null, $ipAddress = null) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $user = $userId ?: (Session::getInstance()->getUserId() ?: 'guest');

        $logEntry = "[{$timestamp}] [{$ip}] [{$user}] {$message}" . PHP_EOL;

        file_put_contents(self::$logPath . 'access.log', $logEntry, FILE_APPEND | LOCK_EX);
    }

    public static function audit($action, $userId = null, $table = null, $recordId = null, $oldValues = null, $newValues = null) {
        $db = Database::getInstance()->getConnection();

        $auditData = [
            'user_id' => $userId ?: Session::getInstance()->getUserId(),
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];

        $db->insert('audit_logs', $auditData);
    }

    private static function log($level, $message, $context = []) {
        $config = require 'config/app.php';

        // Check if logging is enabled for this level
        $logLevels = [
            self::ERROR => 1,
            self::WARNING => 2,
            self::INFO => 3,
            self::DEBUG => 4,
        ];

        $currentLevel = $logLevels[$config['log_level']] ?? 1;
        $messageLevel = $logLevels[$level] ?? 1;

        if ($messageLevel > $currentLevel) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        $logFile = self::$logPath . 'error.log';
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    public static function getLogs($type = 'error', $limit = 100) {
        $logFile = self::$logPath . $type . '.log';

        if (!file_exists($logFile)) {
            return [];
        }

        $lines = file($logFile);
        $lines = array_reverse($lines); // Most recent first
        $lines = array_slice($lines, 0, $limit);

        return array_map('trim', $lines);
    }

    public static function clearLogs($type = 'error') {
        $logFile = self::$logPath . $type . '.log';
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
    }

    public static function getAuditLogs($filters = [], $limit = 50) {
        $db = Database::getInstance()->getConnection();

        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $where[] = 'action = ?';
            $params[] = $filters['action'];
        }

        if (!empty($filters['table_name'])) {
            $where[] = 'table_name = ?';
            $params[] = $filters['table_name'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 'created_at >= ?';
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'created_at <= ?';
            $params[] = $filters['date_to'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT al.*, u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            {$whereClause}
            ORDER BY al.created_at DESC
            LIMIT {$limit}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}