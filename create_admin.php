<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$username = 'JovenJDR';
$password = 'JovenDR102103';
$is_admin = true;

try {
    // Check if the admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        echo "<p>Admin user '$username' already exists.</p>";
    } else {
        if (register($username, $password, $is_admin)) {
            echo "<p>Admin account '$username' created successfully!</p>";
        } else {
            echo "<p>Failed to create admin account.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
