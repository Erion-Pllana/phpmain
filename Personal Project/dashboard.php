<?php
include 'config.php';
requireLogin();
$user_id = getCurrentUserId();

// Statistics
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_tasks = $total_stmt->fetchColumn();

$completed_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Completed' AND user_id = ?");
$completed_stmt->execute([$user_id]);
$completed_tasks = $completed_stmt->fetchColumn();

$pending_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Pending' AND user_id = ?");
$pending_stmt->execute([$user_id]);
$pending_tasks = $pending_stmt->fetchColumn();

$priority_stmt = $pdo->prepare("SELECT priority, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY priority");
$priority_stmt->execute([$user_id]);
$priority_data = $priority_stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recent_stmt->execute([$user_id]);
$recent_tasks = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

$completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;

function getPriorityClass($priority){
    return strtolower($priority);
}
function getStatusClass($status){
    return strtolower($status);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background:#f4f5f7; }
    .container { max-width:1200px; margin:0 auto; padding:40px 20px; display:flex; flex-direction:column; gap:30px; }
    .header { display:flex; flex-direction:column; gap:10px; }
    .nav { display:flex; gap:15px; flex-wrap:wrap; }
    .btn-nav { padding:10px 20px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:8px; transition:0.2s; }
    .btn-nav:hover { background:#4338ca; }
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
    .stat-card { background:#fff; padding:20px; border-radius:12px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .stat-number { font-size:2rem; font-weight:700; margin-bottom:5px; }
    .progress-container { background:#fff; padding:20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .progress-bar { width:100%; background:#e2e8f0; height:16px; border-radius:8px; overflow:hidden; }
    .progress-fill { height:100%; background:#4f46e5; width:0%; transition:0.5s; }
    .chart-container { background:#fff; padding:20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .quest-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; }
    .quest-card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); display:flex; flex-direction:column; gap:10px; }
    .quest-header { display:flex; justify-content:space-between; align-items:center; }
    .quest-priority.high { background:#ef4444; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.8rem; }
    .quest-priority.medium { background:#f59e0b; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.8rem; }
    .quest-priority.low { background:#10b981; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.8rem; }
    .quest-status.completed { color:#10b981; font-weight:600; }
    .quest-status.pending { color:#f59e0b; font-weight:600; }
    .quest-actions a { text-decoration:none; padding:5px 10px; border-radius:6px; font-size:0.8rem; }
    .btn-complete { background:#10b981; color:#fff; }
    .btn-edit { background:#3b82f6; color:#fff; }
    .btn-delete { background:#ef4444; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div style="display:flex; align-items:center; gap:20px;">
            <img src="logo.png" alt="Logo" style="height:50px;">
            <h1>📊 Dashboard</h1>
        </div>
        <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <a href="index.php" class="btn-nav">All Quests</a>
        <a href="dashboard.php" class="btn-nav">Dashboard</a>
        <a href="add.php" class="btn-nav">Add New Quest</a>
        <a href="logout.php" class="btn-nav" style="margin-left:auto;background:#ef4444;">Logout</a>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $total_tasks ?></div>
            <div>Total Quests</div>
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

    <!-- Progress + Priority -->
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:30px; flex-wrap:wrap;">
        <div class="progress-container">
            <h3>Overall Progress</h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $completion_percentage ?>%"></div>
            </div>
            <p style="text-align:center; margin-top:10px; font-weight:600;">
                <?= $completed_tasks ?> out of <?= $total_tasks ?> quests completed
            </p>
        </div>

        <div class="progress-container">
            <h3>Priority Distribution</h3>
            <?php if(empty($priority_data)): ?>
                <p style="text-align:center;color:#64748b;">No tasks yet</p>
            <?php else: ?>
                <?php foreach($priority_data as $priority): ?>
                    <div style="margin-bottom:10px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <span><?= $priority['priority'] ?></span>
                            <span><?= $priority['count'] ?></span>
                        </div>
                        <div style="height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                            <div style="height:100%; background:<?= 
                                $priority['priority'] == 'High' ? '#ef4444' : 
                                ($priority['priority'] == 'Medium' ? '#f59e0b' : '#10b981') 
                            ?>; width:<?= $total_tasks>0?($priority['count']/$total_tasks*100):0 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Chart -->
    <div class="chart-container">
        <h3>Quest Status Distribution</h3>
        <canvas id="statusChart" style="max-height:300px;"></canvas>
    </div>

    <!-- Recent Quests -->
    <div class="progress-container">
        <h3>Recent Quests</h3>
        <?php if(empty($recent_tasks)): ?>
            <p style="text-align:center; color:#64748b;">No quests yet. <a href="add.php">Add your first quest!</a></p>
        <?php else: ?>
            <div class="quest-grid">
                <?php foreach($recent_tasks as $task): ?>
                    <div class="quest-card <?= getPriorityClass($task['priority']) ?>">
                        <div class="quest-header">
                            <h3><?= htmlspecialchars($task['title']) ?></h3>
                            <span class="quest-priority <?= getPriorityClass($task['priority']) ?>">
                                <?= $task['priority'] ?>
                            </span>
                        </div>
                        <div class="quest-category">🏷️ <?= htmlspecialchars($task['category']) ?></div>
                        <p><?= htmlspecialchars($task['description']) ?></p>
                        <div class="quest-meta">
                            <span class="quest-status <?= getStatusClass($task['status']) ?>"><?= $task['status'] ?></span>
                            <span class="quest-due">
                                <?= $task['due_date'] ? date('M j, Y', strtotime($task['due_date'])) : 'No due date' ?>
                            </span>
                        </div>
                        <div class="quest-actions">
                            <?php if($task['status']=='Pending'): ?>
                                <a href="edit.php?complete=<?= $task['id'] ?>" class="btn-complete">Complete</a>
                            <?php endif; ?>
                            <a href="edit.php?id=<?= $task['id'] ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?id=<?= $task['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed','Pending'],
            datasets: [{
                data: [<?= $completed_tasks ?>, <?= $pending_tasks ?>],
                backgroundColor: ['#10b981','#f59e0b'],
                borderColor:'#fff',
                borderWidth:2
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
    });
</script>
</body>
</html>
