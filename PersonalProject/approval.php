<?php
session_start();
include 'config.php';
requireLogin();
requireAdmin(); // Only admin can access this

// Handle approval actions
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if (in_array($action, ['accept', 'decline'])) {
        $status = $action === 'accept' ? 'Accepted' : 'Declined';
        $stmt = $pdo->prepare("UPDATE tasks SET approved = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        header("Location: approval.php?message=Task $status successfully!");
        exit;
    }
}

// Fetch all pending tasks
$stmt = $pdo->query("
    SELECT t.*, u.username 
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.approved = 'Pending'
    ORDER BY t.created_at DESC
");
$pendingTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task Approvals</title>
<style>
    body {
        background-color: #0d0d0d;
        color: #fff;
        font-family: "Poppins", sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto;
    }
    h1 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 2.5rem;
    }
    .nav {
        display: flex;
        justify-content: space-between;
        background-color: #111;
        padding: 12px 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
    }
    .nav a {
        color: #fff;
        text-decoration: none;
        margin-right: 15px;
        transition: all 0.2s ease;
    }
    .nav a:hover {
        color: #00bfff;
        transform: scale(1.05);
    }
    .task-card {
        background-color: #1b1b1b;
        padding: 20px;
        border-radius: 18px;
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.05);
        margin-bottom: 20px;
    }
    .task-meta {
        font-size: 0.9rem;
        color: #ccc;
        margin-top: 5px;
    }
    .actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
    }
    .btn {
        padding: 8px 12px;
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-accept {
        background-color: #00cc66;
    }
    .btn-decline {
        background-color: #ff4d4d;
    }
    .btn:hover {
        transform: scale(1.05);
    }
    .no-tasks {
        text-align: center;
        color: #888;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Pending Task Approvals</h1>
    <div class="nav">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="index.php">All Tasks</a>
        </div>
    </div>

    <?php if (empty($pendingTasks)): ?>
        <p class="no-tasks">No pending tasks for approval 🎉</p>
    <?php else: ?>
        <?php foreach ($pendingTasks as $task): ?>
            <div class="task-card">
                <h3><?= htmlspecialchars($task['title']) ?></h3>
                <p><?= htmlspecialchars($task['description'] ?? 'No description') ?></p>
                <div class="task-meta">
                    <span>Created by: <?= htmlspecialchars($task['username']) ?></span> |
                    <span>Priority: <?= htmlspecialchars($task['priority']) ?></span> |
                    <span>Due: <?= $task['due_date'] ? date('M j, Y', strtotime($task['due_date'])) : 'No due date' ?></span>
                </div>
                <div class="actions">
                    <a href="approval.php?action=accept&id=<?= $task['id'] ?>" class="btn btn-accept">Accept</a>
                    <a href="approval.php?action=decline&id=<?= $task['id'] ?>" class="btn btn-decline" onclick="return confirm('Decline this task?')">Decline</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
