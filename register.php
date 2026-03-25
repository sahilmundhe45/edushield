<?php
/**
 * EduShield – Register Page
 */
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: /edushield/student/dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';

    // Validate role (only allow student and admin)
    if (!in_array($role, ['student', 'admin'])) {
        $role = 'student';
    }

    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $result = registerUser($name, $email, $password, $role);
        if ($result['success']) {
            header("Location: /edushield/login.php?success=" . urlencode($result['message']));
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – EduShield</title>
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
            <a href="/edushield/login.php">Login</a>
            <a href="/edushield/register.php" class="active btn btn-primary btn-sm">Register</a>
        </div>
    </div>
</nav>

<main>
    <div class="form-container">
        <div class="form-card">
            <h2>Create Account 🚀</h2>
            <p class="form-subtitle">Join EduShield and start learning today</p>

            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required
                           value="<?php echo e($_POST['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required
                           value="<?php echo e($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="role">Register As</label>
                    <select id="role" name="role">
                        <option value="student" <?php echo (($_POST['role'] ?? '') === 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min 6 characters" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
            </form>

            <p class="form-link">
                Already have an account? <a href="/edushield/login.php">Sign In</a>
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