<?php
namespace App\Models;

use Dotenv\Dotenv;
use mysqli;
use mysqli_stmt;
use RuntimeException;

class Database
{
    private mysqli $conn;

    public function __construct()
    {
        if (!isset($_ENV['DB_HOST'])) {
            Dotenv::createImmutable(__DIR__ . '/../../')->load();
        }

        $host     = $_ENV['DB_HOST']    ?? 'localhost';
        $username = $_ENV['DB_USER']    ?? 'root';
        $password = $_ENV['DB_PASS']    ?? '';
        $db_name  = $_ENV['DB_NAME']    ?? '';
        $port     = $_ENV['DB_PORT']    ?? 3306;
        $charset  = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $this->conn = new mysqli($host, $username, $password, $db_name, (int)$port);
        if ($this->conn->connect_error) {
            throw new RuntimeException('DB Connection failed: '.$this->conn->connect_error);
        }
        if (!$this->conn->set_charset($charset)) {
            throw new RuntimeException('Error setting charset: '.$this->conn->error);
        }
    }

    public function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Prepare failed: '.$this->conn->error);
        }
        return $stmt;
    }

    public function execute(string $sql, string $types = '', array $params = []): mysqli_stmt
    {
        $stmt = $this->prepare($sql);
        if ($types !== '' && $params) {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            throw new RuntimeException('Execute failed: '.$stmt->error);
        }
        return $stmt;
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }

    public function close(): void
    {
        $this->conn->close();
    }
}
