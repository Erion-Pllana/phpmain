<?php
session_start();
include 'config.php';

// Ensure user is logged in and is admin
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'] ?? null;
    $status = 'Pending';

    $sql = "INSERT INTO tasks (title, description, category, priority, due_date, status, created_at, user_id)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $description, $category, $priority, $due_date, $status, $_SESSION['user_id']])) {
        header('Location: index.php?message=Task added successfully!');
        exit;
    } else {
        $error = "Error adding task. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Task</title>
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
            max-width: 700px;
            margin: 50px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .header p {
            color: #aaa;
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

        .form-container {
            background: #1b1b1b;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-container:hover {
            transform: scale(1.02);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #ccc;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: none;
            background: #111;
            color: #fff;
            font-size: 1rem;
            transition: all 0.25s ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 8px #00bfff;
        }

        textarea {
            resize: none;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.25s ease;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .btn-cancel {
            background: #333;
            color: #fff;
        }

        .btn-cancel:hover {
            background: #ff4d4d;
            color: #000;
        }

        .btn-add {
            background: #00bfff;
            color: #000;
        }

        .btn-add:hover {
            background: #fff;
            color: #000;
            transform: scale(1.08);
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Add New Task</h1>
            <p>Create a new task for your goals</p>
        </div>

        <div class="nav">
            <a href="index.php">← Back to Tasks</a>
            <a href="dashboard.php">Dashboard</a>
        </div>

        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="title">Task Title *</label>
                    <input type="text" id="title" name="title" required maxlength="150">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Describe your task..."></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" maxlength="150"
                        placeholder="e.g., Work, Personal, Learning">
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
                    <a href="index.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-add">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>