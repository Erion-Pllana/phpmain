<?php
include 'config.php';
requireLogin();

$user_id = getCurrentUserId();
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'user'; // 'admin' or 'user'

// ---------------------------
// Fetch statistics
// ---------------------------
if ($role === 'admin') {
    $total_tasks = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
    $completed_tasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'Completed'")->fetchColumn();
    $pending_tasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'Pending'")->fetchColumn();
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_tasks = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Completed' AND user_id = ?");
    $stmt->execute([$user_id]);
    $completed_tasks = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Pending' AND user_id = ?");
    $stmt->execute([$user_id]);
    $pending_tasks = $stmt->fetchColumn();
}

$completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;

// ---------------------------
// Fetch recent tasks
// ---------------------------
if ($role === 'admin') {
    $recent_stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC LIMIT 5");
    $recent_tasks = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $recent_stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $recent_stmt->execute([$user_id]);
    $recent_tasks = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* === Keep your previous CSS exactly as it was === */
        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0a0a;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 2.5em;
            color: #fff;
        }

        .header p {
            color: #aaa;
            margin-top: 10px;
        }

        .nav {
            display: flex;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 12px 20px;
            border-radius: 12px;
            gap: 20px;
            margin-bottom: 40px;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 10px;
            transition: 0.3s ease;
        }

        .nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #fff;
        }

        .progress-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 18px;
            padding: 25px;
            backdrop-filter: blur(10px);
            transition: 0.3s ease;
            margin-bottom: 40px;
        }

        .progress-container:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.1);
            height: 12px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            background: linear-gradient(90deg, #4ade80, #22d3ee);
            height: 100%;
            transition: width 0.6s ease;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 18px;
            backdrop-filter: blur(10px);
            margin-bottom: 40px;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            transition: 0.3s ease;
        }

        .task-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
        }

        .task-card h3 {
            margin: 0;
            color: #fff;
        }

        .task-card p {
            color: #aaa;
            font-size: 0.95em;
        }

        .task-meta {
            display: flex;
            justify-content: space-between;
            color: #bbb;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .btn {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .btn:hover {
            background: #22d3ee;
            transform: scale(1.05);
        }

        .quest-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
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
            transition: 0.25s ease;
            color: #fff;
            text-decoration: none;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Task Dashboard</h1>
            <p>Track your progress and statistics</p>
        </div>

        <div class="nav">
            <a href="index.php">All Tasks</a>
            <a href="dashboard.php">Dashboard</a>
            <?php if ($role === 'admin'): ?>
                <a href="add.php">Add New Task</a>
            <?php endif; ?>
            <span style="margin-left:auto;color:white;">Welcome, <?= htmlspecialchars($username) ?> |
                <a href="logout.php" class="btn">Logout</a>
            </span>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $total_tasks ?></div>
                <div>Total Tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $completed_tasks ?></div>
                <div>Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $pending_tasks ?></div>
                <div>Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $completion_percentage ?>%</div>
                <div>Completion Rate</div>
            </div>
        </div>

        <div class="progress-container">
            <h3>Overall Progress</h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $completion_percentage ?>%"></div>
            </div>
            <p style="text-align:center;margin-top:10px;"><?= $completed_tasks ?> of <?= $total_tasks ?> tasks completed
            </p>
        </div>

        <div class="chart-container">
            <h3>Task Status Distribution</h3>
            <canvas id="statusChart" style="max-height:300px;"></canvas>
        </div>

        <div class="progress-container">
            <h3>Recent Tasks</h3>
            <?php if (empty($recent_tasks)): ?>
                <p style="text-align:center;color:#888;">No tasks yet.</p>
            <?php else: ?>
                <?php foreach ($recent_tasks as $task): ?>
                    <div class="task-card">
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <p><?= htmlspecialchars($task['description']) ?></p>
                        <div class="task-meta">
                            <span>Status: <?= htmlspecialchars($task['status']) ?></span>
                            <span>Due:
                                <?= $task['due_date'] ? date('M j, Y', strtotime($task['due_date'])) : 'No due date' ?></span>
                        </div>
                        <?php if ($role === 'admin'): ?>
                            <div class="quest-actions">
                                <?php if ($task['status'] === 'Pending'): ?>
                                    <a href="edit.php?complete=<?= $task['id'] ?>" class="btn-complete">Complete</a>
                                <?php endif; ?>
                                <a href="edit.php?id=<?= $task['id'] ?>" class="btn-edit">Edit</a>
                                <a href="delete.php?id=<?= $task['id'] ?>" class="btn-delete"
                                    onclick="return confirm('Delete this task?')">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    data: [<?= $completed_tasks ?>, <?= $pending_tasks ?>],
                    backgroundColor: ['#4ade80', '#fbbf24'],
                    borderWidth: 2,
                    borderColor: '#0a0a0a'
                }]
            },
            options: {
                plugins: {
                    legend: { labels: { color: '#fff' }, position: 'bottom' }
                }
            }
        });
    </script>
</body>

</html>