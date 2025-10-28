<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $status = 'Pending';

    $sql = "INSERT INTO tasks (title, description, category, priority, due_date, status, created_at, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
    
$stmt = $pdo->prepare($sql);
    
if ($stmt->execute([$title, $description, $category, $priority, $due_date, $status, getCurrentUserId()])) {
        header('Location: index.php?message=Quest added successfully!');
        exit;
    } else {
        $error = "Error adding quest. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Quest</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Add New Quest</h1>
            <p>Create a new adventure for your journey!</p>
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
                    <input type="text" id="title" name="title" required maxlength="150">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" maxlength="150" placeholder="e.g., Work, Personal, Learning">
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" required>
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date">
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-add">Create Quest</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>