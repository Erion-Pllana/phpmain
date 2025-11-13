<?php
session_start();
include 'config.php';
requireLogin();

// Current user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'user'; // 'admin' or 'user'

// Filters
$filter_priority = $_GET['priority'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_category = $_GET['category'] ?? '';

// Fetch tasks (only approved tasks)
$query = "SELECT * FROM tasks WHERE approved = 'Accepted'";
$params = [];

if ($filter_priority) {
    $query .= " AND priority = :priority";
    $params['priority'] = $filter_priority;
}
if ($filter_status) {
    $query .= " AND status = :status";
    $params['status'] = $filter_status;
}
if ($filter_category) {
    $query .= " AND category = :category";
    $params['category'] = $filter_category;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique categories (for filters)
$catStmt = $pdo->prepare("SELECT DISTINCT category FROM tasks");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch pending tasks (admins only)
$pendingTasks = [];
if ($role === 'admin') {
    $stmt = $pdo->query("
        SELECT t.*, u.username 
        FROM tasks t 
        JOIN users u ON t.user_id = u.id
        WHERE t.approved = 'Pending'
        ORDER BY t.created_at DESC
    ");
    $pendingTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle admin approval actions
if ($role === 'admin' && isset($_GET['approve'], $_GET['id'])) {
    $task_id = (int) $_GET['id'];
    $action = $_GET['approve'];

    if ($action === 'accept') {
        $stmt = $pdo->prepare("UPDATE tasks SET approved = 'Accepted' WHERE id = ?");
        $stmt->execute([$task_id]);
    } elseif ($action === 'decline') {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
    }
    header("Location: index.php");
    exit;
}

// ✅ Handle "Complete" action
if (isset($_GET['complete'])) {
    $task_id = (int) $_GET['complete'];

    // Verify ownership or admin rights
    $stmt = $pdo->prepare("SELECT user_id FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task && ($role === 'admin' || $task['user_id'] == $user_id)) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
        $stmt->execute([$task_id]);
    }

    header("Location: index.php?message=Task+marked+as+completed!");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker</title>
    <style>
        /* ===== COPY YOUR EXISTING STYLE ===== */
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

        .filters {
            margin-bottom: 30px;
            background: #121212;
            padding: 15px 20px;
            border-radius: 12px;
        }

        select,
        .btn {
            background: #1b1b1b;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            margin-right: 10px;
            transition: all 0.25s ease;
        }

        select:hover,
        .btn:hover {
            transform: scale(1.05);
            background: #00bfff;
            color: #000;
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

        .btn-edit,
        .btn-delete,
        .btn-complete {
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

        .btn-edit:hover {
            background: #00bfff;
            color: #000;
        }

        .btn-delete:hover {
            background: #ff4d4d;
            color: #000;
        }

        .btn-complete:hover {
            background: #00cc66;
            color: #000;
        }

        .btn-add {
            background: #00bfff;
            color: #000;
            border-radius: 10px;
            padding: 12px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: scale(1.08);
            background: #fff;
        }

        .status-completed {
            color: #00cc66;
        }

        .status-pending {
            color: #ffcc00;
        }

        .pending-badge {
            background: #ffcc00;
            color: #000;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            margin-top: 8px;
        }

        .admin-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .admin-actions a {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
        }

        .btn-accept {
            background: #00cc66;
        }

        .btn-decline {
            background: #ff4d4d;
        }

        .admin-actions a:hover {
            opacity: 0.9;
            transform: scale(1.03);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Task Tracker</h1>
        <p>Manage your tasks and conquer your goals!</p>
    </div>

    <div class="nav">
        <a href="index.php">All tasks</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="add.php">Add New Task</a>
        <span style="color:#aaa; margin-left:auto;">
            Welcome, <?= htmlspecialchars($username) ?> |
            <a href="logout.php" style="color:#00bfff;">Logout</a>
        </span>
    </div>

    <div class="filters">
        <form method="GET" class="filter-group">
            <select name="priority">
                <option value="">All Priorities</option>
                <option value="High" <?= $filter_priority == 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= $filter_priority == 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= $filter_priority == 'Low' ? 'selected' : '' ?>>Low</option>
            </select>
            <select name="status">
                <option value="">All Status</option>
                <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Completed" <?= $filter_status == 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $filter_category == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Filter</button>
            <a href="index.php" class="btn">Clear</a>
        </form>
    </div>

    <div class="quest-grid">
        <?php if (!empty($pendingTasks)): ?>
            <?php foreach ($pendingTasks as $task): ?>
                <div class="quest-card">
                    <div class="quest-header">
                        <div>
                            <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                            <div class="quest-category">🏷️ <?= htmlspecialchars($task['category']) ?></div>
                        </div>
                        <span class="quest-priority <?= getPriorityClass($task['priority']) ?>"><?= $task['priority'] ?></span>
                    </div>
                    <p class="quest-description"><?= htmlspecialchars($task['description']) ?></p>
                    <div class="quest-meta">
                        <span class="pending-badge">Pending Approval</span>
                        <span>Submitted by: <?= htmlspecialchars($task['username']) ?></span>
                    </div>
                    <div class="admin-actions">
                        <a href="index.php?approve=accept&id=<?= $task['id'] ?>" class="btn-accept">Accept</a>
                        <a href="index.php?approve=decline&id=<?= $task['id'] ?>" class="btn-decline"
                           onclick="return confirm('Decline this task?')">Decline</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($tasks) && empty($pendingTasks)): ?>
            <div class="no-quests" style="grid-column:1 / -1;text-align:center;">
                <h3>No tasks found. <a href="add.php">Add your first task!</a></h3>
            </div>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <?php $isOwner = ($task['user_id'] == $user_id); ?>
            <div class="quest-card <?= getPriorityClass($task['priority']) ?>">
                <div class="quest-header">
                    <div>
                        <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                        <div class="quest-category">🏷️ <?= htmlspecialchars($task['category']) ?></div>
                    </div>
                    <span class="quest-priority <?= getPriorityClass($task['priority']) ?>"><?= $task['priority'] ?></span>
                </div>
                <p class="quest-description"><?= htmlspecialchars($task['description']) ?></p>
                <div class="quest-meta">
                    <span class="<?= getStatusClass($task['status']) ?>"><?= $task['status'] ?></span>
                    <?php if ($task['due_date']): ?>
                        <span>📅 <?= date('M j, Y', strtotime($task['due_date'])) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($role === 'admin' || $isOwner): ?>
                    <div class="quest-actions">
                        <?php if ($task['status'] == 'Pending'): ?>
                            <a href="edit.php?complete=<?= $task['id'] ?>" class="btn-complete">Complete</a>
                        <?php endif; ?>
                        <a href="edit.php?id=<?= $task['id'] ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?= $task['id'] ?>" class="btn-delete"
                           onclick="return confirm('Delete this task?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center; margin-top:35px;">
        <a href="add.php" class="btn-add">➕ Add New Task</a>
    </div>
</div>
</body>
</html>