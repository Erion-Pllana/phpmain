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

// Authentication check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Helper functions
function getPriorityClass($priority) {
    switch($priority) {
        case 'High': return 'priority-high';
        case 'Medium': return 'priority-medium';
        case 'Low': return 'priority-low';
        default: return 'priority-medium';
    }
}

function getStatusClass($status) {
    switch($status) {
        case 'Completed': return 'status-completed';
        case 'Pending': return 'status-pending';
        default: return 'status-pending';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quest Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Quest Tracker</h1>
            <p>Manage your tasks and conquer your goals!</p>
        </div>

        <div class="nav">
    <a href="index.php">All Quests</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="add.php">Add New Quest</a>
    <span style="color: white; margin-left: auto;">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! 
        <a href="logout.php" style="margin-left: 15px;">Logout</a>
    </span>
</div>

        <!-- Filters -->
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
                    <?php foreach($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>" <?= $filter_category == $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn">Filter</button>
                <a href="index.php" class="btn">Clear</a>
            </form>
        </div>

        <!-- Quest Grid -->
        <div class="quest-grid">
            <?php if(empty($tasks)): ?>
                <div class="no-quests" style="text-align: center; color: white; grid-column: 1 / -1;">
                    <h3>No quests found. <a href="add.php" style="color: #fff;">Add your first quest!</a></h3>
                </div>
            <?php else: ?>
                <?php foreach($tasks as $task): ?>
                    <div class="quest-card <?= getPriorityClass($task['priority']) ?>">
                        <div class="quest-header">
                            <div>
                                <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                                <div class="quest-category">🏷️ <?= htmlspecialchars($task['category']) ?></div>
                            </div>
                            <span class="quest-priority <?= getPriorityClass($task['priority']) ?>">
                                <?= $task['priority'] ?>
                            </span>
                        </div>

                        <p class="quest-description"><?= htmlspecialchars($task['description']) ?></p>

                        <div class="quest-meta">
                            <span class="quest-status <?= getStatusClass($task['status']) ?>">
                                <?= $task['status'] ?>
                            </span>
                            <?php if($task['due_date']): ?>
                                <span class="quest-due">
                                    📅 <?= date('M j, Y', strtotime($task['due_date'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="quest-actions">
                            <?php if($task['status'] == 'Pending'): ?>
                                <a href="edit.php?complete=<?= $task['id'] ?>" class="btn btn-complete">Complete</a>
                            <?php endif; ?>
                            <a href="edit.php?id=<?= $task['id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="delete.php?id=<?= $task['id'] ?>" class="btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this quest?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="add.php" class="btn btn-add">➕ Add New Quest</a>
        </div>
    </div>
</body>
</html>