<?php
include 'config.php';

// Handle mark as complete
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php?message=Quest completed!');
    exit;
}

// Handle edit form
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Get current task data
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $sql = "UPDATE tasks SET title = ?, description = ?, category = ?, priority = ?, due_date = ?, status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$title, $description, $category, $priority, $due_date, $status, $id])) {
        header('Location: index.php?message=Quest updated successfully!');
        exit;
    } else {
        $error = "Error updating quest. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quest</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Quest</h1>
            <p>Update your quest details</p>
        </div>

        <div class="nav">
            <a href="index.php">← Back to Quests</a>
            <a href="dashboard.php">Dashboard</a>
        </div>

        <div class="form-container">
            <?php if(isset($error)): ?>
                <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="title">Quest Title *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required maxlength="150">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= htmlspecialchars($task['category']) ?>" maxlength="150">
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" required>
                        <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" value="<?= $task['due_date'] ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-edit">Update Quest</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>