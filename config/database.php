<?php
require_once __DIR__ . '/env.php';

class Database {
    private static ?Database $instance = null;
    private PDO $conn;

    private function __construct() {
        $host   = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? '';
        $user   = $_ENV['DB_USER'] ?? 'root';
        $pass   = $_ENV['DB_PASS'] ?? '';

        $this->conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Force MySQL session timezone to UTC so TIMESTAMP comparisons
                // (expires_at > NOW()) align with PHP's UTC date generation.
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'",
            ]
        );
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->conn;
    }

    private function __clone() {}
    public function __wakeup(): void {
        throw new \RuntimeException('Cannot unserialize singleton.');
    }
}
