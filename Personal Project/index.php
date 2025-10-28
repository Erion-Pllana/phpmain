<?php
session_start();
$host = 'localhost';
$dbname = 'database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Authentication
function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) { header('Location: login.php'); exit; } }
function getCurrentUserId() { return $_SESSION['user_id'] ?? null; }

$filter_priority = $_GET['priority'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_category = $_GET['category'] ?? '';

$user_id = getCurrentUserId();

// Build query
$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];

if ($filter_priority) { $query .= " AND priority = ?"; $params[] = $filter_priority; }
if ($filter_status) { $query .= " AND status = ?"; $params[] = $filter_status; }
if ($filter_category) { $query .= " AND category = ?"; $params[] = $filter_category; }

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->prepare("SELECT DISTINCT category FROM tasks WHERE user_id = ?");
$catStmt->execute([$user_id]);
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task Tracker</title>
<link rel="stylesheet" href="style.css">
<style>
    /* Basic spacing and grid adjustments */
    body { margin: 0; font-family: 'Segoe UI', sans-serif;  background: #f4f5f7; }
    .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; display: flex; flex-direction: column; gap: 30px; }
    .header { display: flex; flex-direction: column; gap: 10px; }
    .nav { display: flex; gap: 15px; flex-wrap: wrap; }
    .btn-nav { padding: 10px 20px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 8px; transition: 0.2s; }
    .btn-nav:hover { background: #4338ca; }
    .filters { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
    .filter-group select, .filter-group button, .filter-group a { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; background: #fff; }
    .task-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
    .task-card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 10px; }
    .task-header { display: flex; justify-content: space-between; align-items: center; }
    .task-meta, .task-actions { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .tag { padding: 3px 10px; border-radius: 12px; font-size: 0.8rem; color: #fff; }
    .high { background: #ef4444; }
    .medium { background: #f59e0b; }
    .low { background: #10b981; }
    .status.pending { color: #f59e0b; }
    .status.completed { color: #10b981; }
    .btn.small { font-size: 0.8rem; padding: 5px 10px; }
</style>
</head>
<body>
<div class="container">
    <!-- Header + Logo -->
    <div class="header">
        <div style="display:flex; align-items:center; gap:20px;">
            <img src="logo.png" alt="Logo" style="height:50px;"> <!-- Placeholder for future logo -->
            <h1>🗂️ Task Tracker</h1>
        </div>
        <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <a href="index.php" class="btn-nav">All Tasks</a>
        <a href="dashboard.php" class="btn-nav">Dashboard</a>
        <a href="add.php" class="btn-nav">Add New Task</a>
        <a href="logout.php" class="btn-nav" style="margin-left:auto;background:#ef4444;">Logout</a>
    </div>

    <!-- Filters -->
    <div class="filters">
        <form method="GET" class="filter-group" style="display:flex; gap:10px; flex-wrap:wrap;">
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
                <?php foreach($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category) ?>" <?= $filter_category == $category ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
            <a href="index.php">Clear</a>
        </form>
    </div>

    <!-- Task Grid -->
    <div class="task-grid">
        <?php if(empty($tasks)): ?>
            <div style="grid-column:1/-1; text-align:center; padding:50px; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                <h3>No tasks found.</h3>
                <a href="add.php" class="btn-nav" style="margin-top:15px; display:inline-block;">➕ Add your first task</a>
            </div>
        <?php else: ?>
            <?php foreach($tasks as $task): ?>
                <div class="task-card">
                    <div class="task-header">
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <span class="tag <?= strtolower($task['priority']) ?>"><?= htmlspecialchars($task['priority']) ?></span>
                    </div>
                    <p><?= htmlspecialchars($task['description']) ?></p>
                    <div class="task-meta">
                        <span class="status <?= strtolower($task['status']) ?>"><?= htmlspecialchars($task['status']) ?></span>
                        <?php if($task['due_date']): ?>
                            <span>📅 <?= date('M j, Y', strtotime($task['due_date'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="task-actions">
                        <?php if($task['status'] == 'Pending'): ?>
                            <a href="edit.php?complete=<?= $task['id'] ?>" class="btn small">Complete</a>
                        <?php endif; ?>
                        <a href="edit.php?id=<?= $task['id'] ?>" class="btn small">Edit</a>
                        <a href="delete.php?id=<?= $task['id'] ?>" class="btn small" onclick="return confirm('Delete this task?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
