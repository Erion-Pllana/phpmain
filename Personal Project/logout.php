<?php
session_start();

// Get username before destroying session (optional)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Destroy session
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodbye - Task Tracker</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: white;
            overflow: hidden;
        }

        .goodbye-card {
            background: #ffffff;
            color: #111827;
            padding: 40px 60px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: fadeIn 0.8s ease;
        }

        .goodbye-card h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .goodbye-card p {
            font-size: 1rem;
            color: #6b7280;
        }

        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-out {
            animation: fadeOut 0.6s ease forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); }
        }

        .background-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.07), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 4s infinite alternate ease-in-out;
        }

        @keyframes pulse {
            from { transform: translate(-50%, -50%) scale(1); opacity: 0.6; }
            to { transform: translate(-50%, -50%) scale(1.2); opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="background-glow"></div>

    <div class="goodbye-card fade-in" id="goodbyeCard">
        <h1>Done for the day</h1>
        <p>See you next time<?= $username ? ", <strong>" . htmlspecialchars($username) . "</strong>" : "" ?>.</p>
    </div>

    <script>
        // Wait, then fade out and redirect
        setTimeout(() => {
            document.getElementById('goodbyeCard').classList.add('fade-out');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 600);
        }, 1500);
    </script>
</body>
</html>
