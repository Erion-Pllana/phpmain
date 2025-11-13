<?php
session_start();
include 'config.php';
requireLogin();
requireAdmin(); // Only admin can access this page

// Handle approve/decline actions
if (isset($_GET['action'], $_GET['id'])) {
    $task_id = (int) $_GET['id'];
    $action = $_GET['action'];

    if (in_array($action, ['accept', 'decline'])) {
        if ($action === 'accept') {
            $stmt = $pdo->prepare("UPDATE tasks SET approved = 'Accepted' WHERE id = ?");
            $stmt->execute([$task_id]);
        } else {
            // Decline → delete task immediately
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
        }
    }

    header('Location: approve_tasks.php');
    exit;
}

// Fetch all pending tasks
$stmt = $pdo->query("
    SELECT t.*, u.username 
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.approved = 'Pending'
    ORDER BY t.created_at DESC
");
$pending_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Approval Panel</title>
    <style>
        body {
            background-color: #0d0d0d;
            font-family: "Poppins", sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.8rem;
            color: #fff;
            margin-bottom: 5px;
        }

        .header p {
            color: #ccc;
        }

        .nav {
            display: flex;
            align-items: center;
            background: #111;
            padding: 12px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 25px;
            transition: all 0.25s ease;
        }

        .nav a:hover {
            transform: scale(1.1);
            color: #00bfff;
        }

        .quest-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
            gap: 20px;
        }

        .quest-card {
            background: #1b1b1b;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .quest-card:hover {
            transform: scale(1.04);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.3);
        }

        .quest-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quest-title {
            font-size: 1.3rem;
            color: white;
        }

        .quest-category {
            font-size: 0.9rem;
            color: #aaa;
        }

        .quest-priority {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .priority-high {
            background: #ff4d4d;
        }

        .priority-medium {
            background: #ffcc00;
            color: #000;
        }

        .priority-low {
            background: #00cc66;
        }

        .quest-description {
            margin: 15px 0;
            color: #ccc;
        }

        .quest-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #aaa;
        }

        .quest-actions {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn-approve,
        .btn-decline {
            flex: 1;
            text-align: center;
            margin: 0 5px;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            transition: all 0.25s ease;
        }

        .btn-approve {
            background: #00cc66;
        }

        .btn-approve:hover {
            background: #00ff99;
            color: #000;
        }

        .btn-decline {
            background: #ff4d4d;
        }

        .btn-decline:hover {
            background: #ff6666;
            color: #000;
        }

        .no-quests {
            grid-column: 1 / -1;
            text-align: center;
            color: #aaa;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Task Approval Panel</h1>
            <p>Review tasks submitted by users and approve or decline them.</p>
        </div>

        <div class="nav">
            <a href="index.php">← All Tasks</a>
            <a href="dashboard.php">Dashboard</a>
            <span style="color:#aaa; margin-left:auto;">
                Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> |
                <a href="logout.php" style="color:#00bfff;">Logout</a>
            </span>
        </div>

        <div class="quest-grid">
            <?php if (empty($pending_tasks)): ?>
                <div class="no-quests">No pending tasks at the moment 🎉</div>
            <?php else: ?>
                <?php foreach ($pending_tasks as $task): ?>
                    <div class="quest-card <?= getPriorityClass($task['priority']) ?>">
                        <div class="quest-header">
                            <div>
                                <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                                <div class="quest-category">🏷️ <?= htmlspecialchars($task['category'] ?: 'No category') ?>
                                </div>
                            </div>
                            <span
                                class="quest-priority <?= getPriorityClass($task['priority']) ?>"><?= $task['priority'] ?></span>
                        </div>
                        <p class="quest-description"><?= htmlspecialchars($task['description'] ?: 'No description') ?></p>
                        <div class="quest-meta">
                            <span>Status: Pending</span>
                            <?php if ($task['due_date']): ?>
                                <span>📅 <?= date('M j, Y', strtotime($task['due_date'])) ?></span>
                            <?php endif; ?>
                            <span>User: <?= htmlspecialchars($task['username']) ?></span>
                        </div>
                        <div class="quest-actions">
                            <a href="approve_tasks.php?action=accept&id=<?= $task['id'] ?>" class="btn-approve">Approve</a>
                            <a href="approve_tasks.php?action=decline&id=<?= $task['id'] ?>" class="btn-decline"
                                onclick="return confirm('Decline this task?')">Decline</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>