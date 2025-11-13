<?php
session_start();
include 'config.php';

// Make sure only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle accept/decline
if (isset($_GET['approve']) || isset($_GET['decline'])) {
    $task_id = isset($_GET['approve']) ? $_GET['approve'] : $_GET['decline'];
    $new_status = isset($_GET['approve']) ? 'Accepted' : 'Declined';
    $stmt = $pdo->prepare("UPDATE tasks SET approved = :status WHERE id = :id");
    $stmt->execute(['status' => $new_status, 'id' => $task_id]);
    header("Location: requests.php");
    exit;
}

// Get pending tasks
$stmt = $pdo->prepare("SELECT t.*, u.username 
                       FROM tasks t 
                       JOIN users u ON t.user_id = u.id 
                       WHERE t.approved = 'Pending' 
                       ORDER BY t.created_at DESC");
$stmt->execute();
$pending_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Task Requests</title>
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
            font-size: 2.5rem;
            color: #fff;
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
        .btn {
            flex: 1;
            text-align: center;
            margin: 0 5px;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            background: #111;
            transition: all 0.25s ease;
            text-decoration: none;
            color: #fff;
        }
        .btn-accept:hover {
            background: #00cc66;
            color: #000;
        }
        .btn-decline:hover {
            background: #ff4d4d;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pending Task Requests</h1>
            <p>Approve or decline new user tasks</p>
        </div>

        <div class="nav">
            <a href="index.php">All Tasks</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="requests.php" style="color:#00bfff;">Requests</a>
            <span style="color:#aaa; margin-left:auto;">
                Admin Panel | <a href="logout.php" style="color:#00bfff;">Logout</a>
            </span>
        </div>

        <div class="quest-grid">
            <?php if (empty($pending_tasks)): ?>
                <div style="grid-column:1 / -1;text-align:center;">
                    <h3>No pending requests ✅</h3>
                </div>
            <?php else: ?>
                <?php foreach ($pending_tasks as $task): ?>
                    <div class="quest-card">
                        <div class="quest-header">
                            <div>
                                <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                                <div class="quest-category">By <?= htmlspecialchars($task['username']) ?></div>
                            </div>
                        </div>
                        <p class="quest-description"><?= htmlspecialchars($task['description']) ?></p>
                        <div class="quest-meta">
                            <span>Priority: <?= htmlspecialchars($task['priority']) ?></span>
                            <span>Category: <?= htmlspecialchars($task['category']) ?></span>
                        </div>
                        <div class="quest-actions">
                            <a href="requests.php?approve=<?= $task['id'] ?>" class="btn btn-accept">✅ Accept</a>
                            <a href="requests.php?decline=<?= $task['id'] ?>" class="btn btn-decline"
                               onclick="return confirm('Decline this task?')">❌ Decline</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
