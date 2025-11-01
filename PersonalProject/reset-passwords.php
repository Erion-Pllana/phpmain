<?php
$host = 'localhost';
$dbname = 'task_tracker';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hash new passwords
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $userHash = password_hash('user123', PASSWORD_DEFAULT);

    // Update users
    $pdo->exec("UPDATE users SET password='$adminHash' WHERE username='admin'");
    $pdo->exec("UPDATE users SET password='$userHash'  WHERE username='user'");

    echo "Passwords reset successfully.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>