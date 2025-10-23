<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Handle new task submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $priority = $_POST["priority"];
    $due_date = $_POST["due_date"];
    $status = "Pending";
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO tasks (title, description, category, priority, due_date, status, created_at)
            VALUES ('$title', '$description', '$category', '$priority', '$due_date', '$status', '$created_at')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch tasks
$tasks = $conn->query("SELECT * FROM tasks ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TaskFlow Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background: #f7f8ff;
            font-family: 'Poppins', sans-serif;
        }

        header {
            background: linear-gradient(135deg, #6B73FF, #000DFF);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
        }

        .logout {
            background: white;
            color: #000DFF;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout:hover {
            background: #f4f4f4;
        }

        .container {
            padding: 40px;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: inherit;
        }

        button {
            background: #000DFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #6B73FF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #000DFF;
            color: white;
        }

        tr:hover {
            background: #f4f6ff;
        }

        .status-pending {
            color: #ff9800;
            font-weight: 600;
        }

        .status-completed {
            color: #4CAF50;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <header>
        <h2>🗓️ TaskFlow - Daily Task Manager</h2>
        <a href="logout.php" class="logout">Logout</a>
    </header>

    <div class="container">
        <h3>Add New Task</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Task title" required>
            <textarea name="description" placeholder="Task description" rows="3" required></textarea>
            <input type="text" name="category" placeholder="Category (e.g. Work, Personal)" required>

            <label>Priority:</label>
            <select name="priority">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
            </select>

            <label>Due Date:</label>
            <input type="date" name="due_date" required>

            <button type="submit">Add Task</button>
        </form>

        <h3>Your Tasks</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $tasks->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["title"]) ?></td>
                    <td><?= htmlspecialchars($row["category"]) ?></td>
                    <td><?= htmlspecialchars($row["priority"]) ?></td>
                    <td><?= htmlspecialchars($row["due_date"]) ?></td>
                    <td class="<?= $row["status"] == 'Completed' ? 'status-completed' : 'status-pending' ?>">
                        <?= htmlspecialchars($row["status"]) ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>