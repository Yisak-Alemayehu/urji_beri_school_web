<?php
/**
 * Database Configuration
 * Urji Beri School Website
 * 
 * This file contains database connection settings and creates
 * a PDO connection instance for use throughout the application.
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}

// Database configuration (from .env)
define('DB_HOST', (string) env('DB_HOST', 'localhost'));
define('DB_NAME', (string) env('DB_NAME', 'urji_beri_school'));
define('DB_USER', (string) env('DB_USER', 'root'));
define('DB_PASS', (string) env('DB_PASS', ''));
define('DB_CHARSET', (string) env('DB_CHARSET', 'utf8mb4'));

// PDO options for secure connections
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
];

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}

/**
 * Database helper class for common operations
 */
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return results
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch all results
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Fetch single row
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Get row count
     */
    public function rowCount($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
}
