<?php
session_start();
include 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Only allow owners or admins
$stmt = $pdo->prepare("SELECT user_id FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task || ($role !== 'admin' && $task['user_id'] != $user_id)) {
    header('Location: index.php');
    exit;
}

// Mark as completed
$stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php?message=Task+marked+as+completed!');
exit;
