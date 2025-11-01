<?php
session_start();
include 'config.php';

// Ensure user is logged in
requireLogin();

// Only admins can delete
requireAdmin();

$id = $_GET['id'] ?? null;

if (!$id) {
    // No task ID provided
    header('Location: index.php');
    exit;
}

// Delete task
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: index.php?message=Task deleted successfully!');
} else {
    header('Location: index.php?error=Error deleting task.');
}
exit;
?>