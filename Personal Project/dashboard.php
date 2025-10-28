<?php
include 'config.php';
requireLogin();

// Get statistics for current user only
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$total_stmt->execute([getCurrentUserId()]);
$total_tasks = $total_stmt->fetchColumn();

$completed_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Completed' AND user_id = ?");
$completed_stmt->execute([getCurrentUserId()]);
$completed_tasks = $completed_stmt->fetchColumn();

$pending_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Pending' AND user_id = ?");
$pending_stmt->execute([getCurrentUserId()]);
$pending_tasks = $pending_stmt->fetchColumn();

// Priority distribution for current user
$priority_stmt = $pdo->prepare("SELECT priority, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY priority");
$priority_stmt->execute([getCurrentUserId()]);
$priority_data = $priority_stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent tasks for current user
$recent_stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recent_stmt->execute([getCurrentUserId()]);
$recent_tasks = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

// Completion percentage
$completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quest Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Quest Dashboard</h1>
            <p>Track your progress and statistics</p>
        </div>

        <div class="nav">
            <a href="index.php">All Quests</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="add.php">Add New Quest</a>
            <span style="color: white; margin-left: auto;">
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! 
                <a href="logout.php" style="margin-left: 15px; background: rgba(255,255,255,0.3);">Logout</a>
            </span>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number stat-total"><?= $total_tasks ?></div>
                <div>Total Quests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-completed"><?= $completed_tasks ?></div>
                <div>Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-pending"><?= $pending_tasks ?></div>
                <div>Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $completion_percentage ?>%</div>
                <div>Completion Rate</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- Progress Bar -->
            <div class="progress-container">
                <h3>Overall Progress</h3>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $completion_percentage ?>%"></div>
                </div>
                <p style="text-align: center; margin-top: 10px; font-weight: 600;">
                    <?= $completed_tasks ?> out of <?= $total_tasks ?> quests completed
                </p>
            </div>

            <!-- Priority Overview -->
            <div class="progress-container">
                <h3>Priority Distribution</h3>
                <?php if(empty($priority_data)): ?>
                    <p style="text-align: center; color: #64748b;">No tasks yet</p>
                <?php else: ?>
                    <?php foreach($priority_data as $priority): ?>
                        <div style="margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span><?= $priority['priority'] ?></span>
                                <span><?= $priority['count'] ?></span>
                            </div>
                            <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: <?= 
                                    $priority['priority'] == 'High' ? 'var(--high)' : 
                                    ($priority['priority'] == 'Medium' ? 'var(--medium)' : 'var(--low)') 
                                ?>; width: <?= $total_tasks > 0 ? ($priority['count'] / $total_tasks * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="chart-container">
            <h3>Quest Status Distribution</h3>
            <canvas id="statusChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Recent Quests -->
        <div class="progress-container">
            <h3>Recent Quests</h3>
            <?php if(empty($recent_tasks)): ?>
                <p style="text-align: center; color: #64748b;">No quests yet. <a href="add.php">Add your first quest!</a></p>
            <?php else: ?>
                <div class="quest-grid" style="grid-template-columns: 1fr;">
                    <?php foreach($recent_tasks as $task): ?>
                        <div class="quest-card <?= getPriorityClass($task['priority']) ?>">
                            <div class="quest-header">
                                <h3 class="quest-title"><?= htmlspecialchars($task['title']) ?></h3>
                                <span class="quest-priority <?= getPriorityClass($task['priority']) ?>">
                                    <?= $task['priority'] ?>
                                </span>
                            </div>
                            <div class="quest-category">🏷️ <?= htmlspecialchars($task['category']) ?></div>
                            <p class="quest-description"><?= htmlspecialchars($task['description']) ?></p>
                            <div class="quest-meta">
                                <span class="quest-status <?= getStatusClass($task['status']) ?>">
                                    <?= $task['status'] ?>
                                </span>
                                <span class="quest-due">
                                    <?php if($task['due_date']): ?>
                                        📅 <?= date('M j, Y', strtotime($task['due_date'])) ?>
                                    <?php else: ?>
                                        No due date
                                    <?php endif; ?>
                                </span>
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
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Chart for status distribution
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    data: [<?= $completed_tasks ?>, <?= $pending_tasks ?>],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>