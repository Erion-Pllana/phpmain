<?php
include 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            $success = "Account created successfully! You can now log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Quest Tracker</title>
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 18px 20px;
            border-radius: 15px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 1.1rem;
            color: var(--text-color);
            transition: all 0.4s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        input:hover,
        input:focus {
            outline: none;
            border-color: #9ca3af;
            background: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background: var(--button-hover);
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
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
            transition: color 0.3s ease;
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
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
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

        @keyframes fadeIn {
            to {
                opacity: 1;
            }

            from {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Sign Up</h1>
                <p>Create a new account</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?= $success ?></div>
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
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit">Sign Up</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>

</html>