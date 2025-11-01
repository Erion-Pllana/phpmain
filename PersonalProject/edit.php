<?php
session_start();
include 'config.php';

requireLogin();

// Mark task as completed (both admin and normal users)
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php?message=Task completed!');
    exit;
}

// Admin-only access
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

requireAdmin(); // Only admin can edit task details

$id = $_GET['id'];

// Fetch task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare(
        "UPDATE tasks SET title = ?, description = ?, category = ?, priority = ?, due_date = ?, status = ? WHERE id = ?"
    );

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
        /* GENERAL */
        body {
            background-color: #0d0d0d;
            color: #fff;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
        }

        /* HEADER */
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

        /* NAVIGATION */
        .nav {
            display: flex;
            align-items: center;
            background-color: #111;
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

        /* FORM CONTAINER */
        .form-container {
            background-color: #1b1b1b;
            padding: 25px 30px;
            border-radius: 18px;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.05);
        }

        /* FORM GROUPS */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #fff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            background-color: #111;
            color: #fff;
            border: 2px solid #222;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.25s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #00bfff;
            outline: none;
            background-color: #111;
        }

        /* FORM ACTIONS */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        /* BUTTONS */
        .btn {
            flex: 1;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            background-color: #111;
            color: #fff;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn:hover {
            transform: scale(1.05);
            background-color: #00bfff;
            color: #000;
        }

        .btn-edit {
            background-color: #00cc66;
            color: #000;
        }

        .btn-edit:hover {
            background-color: #00ff88;
            transform: scale(1.05);
        }

        /* ERROR MESSAGE */
        .error-message {
            background-color: rgba(255, 77, 77, 0.2);
            color: #ff9999;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1> Edit Task</h1>
            <p>Update your task details</p>
        </div>

        <div class="nav">
            <a href="index.php">← Back to Tasks</a>
            <a href="dashboard.php">Dashboard</a>
        </div>

        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="title">Task Title *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required
                        maxlength="150">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                        rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= htmlspecialchars($task['category']) ?>"
                        maxlength="150">
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
                        <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-edit">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>