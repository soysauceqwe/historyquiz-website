<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);




// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'history_quiz');

// Create connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();
?><?php
// Database configuration
$host = 'localhost';
$db   = 'history_quiz';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Set default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation of prepared statements
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Handle connection errors
    echo "Database connection failed: " . $e->getMessage();
    exit();
}
?>
