<?php
include 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear previous login errors for fresh login page
$errors = [];

// Only redirect if the user is already logged in and session is valid
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($usernameOrEmail) || empty($password)) {
        $errors[] = "Please enter both username/email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = "Welcome back, " . $user['username'] . "!";
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Invalid username/email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quest Tracker</title>
    <style>
        :root {
            --bg-color: #0a0a0a;
            --card-bg: #ffffff;
            --text-color: #111111;
            --accent-color: #9ca3af;
            --button-color: #f3f4f6;
            --button-hover: #e5e7eb;
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .auth-card {
            background: var(--card-bg);
            padding: 60px 40px;
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            animation: floatCard 6s ease-in-out infinite;
        }

        .auth-card:hover {
            transform: scale(1.05);
            box-shadow: 0 35px 80px rgba(0, 0, 0, 0.3);
        }

        @keyframes floatCard {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .auth-header h1 {
            margin: 0 0 15px 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .auth-header p {
            color: var(--accent-color);
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
            color: var(--accent-color);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 18px 20px;
            border-radius: 15px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 1.1rem;
            color: var(--text-color);
            transition: all 0.4s ease;
        }

        input[type="text"]:hover,
        input[type="password"]:hover,
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #9ca3af;
            background: #ffffff;
            transform: scale(1.02);
        }

        button {
            width: 100%;
            padding: 18px;
            border-radius: 15px;
            border: none;
            background: var(--button-color);
            font-weight: 700;
            color: var(--text-color);
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        button:hover {
            background: var(--button-hover);
            transform: scale(1.05);
        }

        .auth-footer {
            text-align: center;
            margin-top: 35px;
            font-size: 1rem;
            color: var(--accent-color);
        }

        .auth-footer a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-footer a:hover {
            color: #9ca3af;
        }

        .error-message,
        .success-message {
            padding: 16px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .success-message {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Login</h1>
                <p>Welcome back to Quest Tracker</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Login</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            </div>
        </div>
    </div>
</body>

</html>