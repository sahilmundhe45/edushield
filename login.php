<?php
/**
 * EduShield – Login Page
 */
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getCurrentUserRole();
    header("Location: /edushield/" . ($role === 'admin' ? 'admin' : 'student') . "/dashboard.php");
    exit();
}

$error = '';
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($email, $password);

    if ($result['success']) {
        $redirect = ($result['role'] === 'admin') ? '/edushield/admin/dashboard.php' : '/edushield/student/dashboard.php';
        header("Location: $redirect");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – EduShield</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo">
            <span>🛡️</span>
            <span>EduShield</span>
        </a>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <a href="/edushield/login.php" class="active">Login</a>
            <a href="/edushield/register.php" class="btn btn-primary btn-sm">Register</a>
        </div>
    </div>
</nav>

<main>
    <div class="form-container">
        <div class="form-card">
            <h2>Welcome Back 👋</h2>
            <p class="form-subtitle">Sign in to continue your learning journey</p>

            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?php echo e($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-error">⚠️ <?php echo e($_GET['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required 
                           value="<?php echo e($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
            </form>

            <p class="form-link">
                Don't have an account? <a href="/edushield/register.php">Create one</a>
            </p>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EduShield. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="js/scripts.js"></script>
</body>
</html>
