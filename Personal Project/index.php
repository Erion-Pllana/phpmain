<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow | Your Daily Task Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #6B73FF, #000DFF);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .hero {
            text-align: center;
            max-width: 700px;
            padding: 40px;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 15px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 40px;
            color: #e0e0e0;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        a.btn {
            background: white;
            color: #000DFF;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        a.btn:hover {
            background: #f4f4f4;
        }

        footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            background: url('https://i.imgur.com/eaKq7ZG.png');
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to <span style="color:#FFD700;">TaskFlow</span></h1>
        <p>Organize your day. Boost your productivity. Stay focused on what matters most.</p>
        <div class="btn-group">
            <a href="login.php" class="btn">Log In</a>
            <a href="#" class="btn" style="background:#FFD700; color:#000;">Sign Up</a>
        </div>
    </div>

    <footer>
        © <?= date('Y') ?> TaskFlow. Your Daily Task Manager.
    </footer>
</body>
</html>