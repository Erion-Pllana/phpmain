<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'tasktracker'; // updated database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// -------------------------
// Authentication functions
// -------------------------
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

// -------------------------
// Role helpers
// -------------------------
function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Only call this on admin-only pages
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// -------------------------
// UI helper functions
// -------------------------
function getPriorityClass(string $priority): string
{
    switch ($priority) {
        case 'High':
            return 'priority-high';
        case 'Medium':
            return 'priority-medium';
        case 'Low':
            return 'priority-low';
        default:
            return 'priority-medium';
    }
}

function getStatusClass(string $status): string
{
    switch ($status) {
        case 'Completed':
            return 'status-completed';
        case 'Pending':
            return 'status-pending';
        default:
            return 'status-pending';
    }
}
?>
