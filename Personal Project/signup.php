<?php
include 'config.php';

// Redirect if logged in
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
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        // Check if username or email already exist
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION['success'] = "Account created successfully! You can now log in.";
                header('Location: login.php');
                exit;
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Task Tracker</title>
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --black: #111827;
            --dark-gray: #1f2937;
            --gray: #6b7280;
            --light-gray: #e5e7eb;
            --white: #ffffff;
            --accent: #2563eb;
            --danger: #dc2626;
            --success: #16a34a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--black);
            overflow: hidden;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 0 20px;
            animation: fadeIn 0.8s ease-out;
        }

        .auth-card {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 10px;
        }

        .auth-header p {
            color: var(--gray);
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeIn 1s ease;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
            color: var(--black);
        }

        input:focus {
            border-color: var(--black);
            background: #fff;
            outline: none;
            box-shadow: 0 0 8px rgba(0,0,0,0.15);
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            color: white;
            background: var(--black);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--dark-gray);
            transform: translateY(-2px);
        }

        .error-message, .success-message {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            animation: fadeIn 0.5s ease;
        }

        .error-message {
            background: #fee2e2;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .success-message {
            background: #dcfce7;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }

        .auth-footer a {
            color: var(--black);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: var(--accent);
        }

        .background-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.05), transparent 70%);
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            animation: float 8s ease-in-out infinite alternate;
        }

        @keyframes float {
            from { transform: translate(-50%, -10px); }
            to { transform: translate(-50%, 10px); }
        }
    </style>
</head>
<body>
    <div class="background-glow"></div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Sign up to get started with Task Tracker</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success) ?>
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
                    <input type="email" id="email" name="email" 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn">Sign Up</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Log in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
