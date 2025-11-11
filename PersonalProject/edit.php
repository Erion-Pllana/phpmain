<?php
session_start();
include 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Get task ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch the task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

// Normal user can only edit their own task
if ($role !== 'admin' && $task['user_id'] != $user_id) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, category = ?, priority = ?, due_date = ?, status = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $category, $priority, $due_date, $status, $id])) {
        header('Location: index.php?message=Task updated successfully!');
        exit;
    } else {
        $error = "Error updating task. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #fff;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5rem;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            background-color: #111;
            padding: 12px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 15px;
            transition: all 0.2s ease;
        }
        .nav a:hover {
            color: #00bfff;
            transform: scale(1.05);
        }
        .form-container {
            background-color: #1b1b1b;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 0 25px rgba(0,191,255,0.2);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        input[type="text"], input[type="date"], select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #111;
            color: #fff;
            font-size: 1rem;
            transition: all 0.25s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 8px #00bfff;
        }
        textarea {
            resize: none;
        }
        .btn {
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .btn-edit {
            background-color: #00bfff;
            color: #000;
        }
        .btn-edit:hover {
            background-color: #33ccff;
            transform: scale(1.05);
        }
        .btn-cancel {
            background-color: #333;
            color: #fff;
        }
        .btn-cancel:hover {
            background-color: #ff4d4d;
            color: #000;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }
        .error-message {
            background-color: rgba(255,77,77,0.2);
            color: #ff9999;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Task</h1>
    <div class="nav">
        <div>
            <a href="index.php">← Back to Tasks</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </div>

    <div class="form-container">
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Task Title *</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($task['category']) ?>">
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
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
                <select id="status" name="status">
                    <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-edit">Update Task</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
