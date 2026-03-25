<?php
/**
 * EduShield – Enrollment / Payment Page
 */
require_once 'includes/session.php';
require_once 'includes/functions.php';

requireLogin();
requireStudent();

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = getCourseById($courseId);

if (!$course) {
    header("Location: /edushield/?error=" . urlencode("Course not found."));
    exit();
}

$userId = getCurrentUserId();

// Already enrolled?
if (isEnrolled($userId, $courseId)) {
    header("Location: /edushield/course.php?id=$courseId&success=" . urlencode("You are already enrolled in this course."));
    exit();
}

$paymentSuccess = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    // Simulate payment processing
    $cardNumber = $_POST['card_number'] ?? '';
    $cardName = $_POST['card_name'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Basic validation
    if (empty($cardNumber) || empty($cardName) || empty($expiry) || empty($cvv)) {
        $error = 'Please fill in all payment fields.';
    } else {
        // Process simulated payment
        $paymentResult = processPayment($userId, $courseId, $course['price']);
        if ($paymentResult) {
            // Enroll user
            enrollUser($userId, $courseId);
            // Initialize progress
            updateProgress($userId, $courseId, 0);
            $paymentSuccess = true;
        } else {
            $error = 'Payment processing failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll – <?php echo e($course['title']); ?> – EduShield</title>
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
            <a href="/edushield/student/dashboard.php">Dashboard</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <div class="payment-container">
        <?php if ($paymentSuccess): ?>
            <div class="payment-card" style="animation: fadeInUp 0.5s ease;">
                <div class="payment-icon">🎉</div>
                <h2 style="color: var(--success); margin-bottom: 12px;">Payment Successful!</h2>
                <p style="color: var(--text-secondary); margin-bottom: 8px;">You are now enrolled in</p>
                <h3 style="margin-bottom: 24px;"><?php echo e($course['title']); ?></h3>
                <a href="/edushield/course.php?id=<?php echo $courseId; ?>" class="btn btn-primary btn-lg">Start Learning →</a>
            </div>
        <?php else: ?>
            <div class="payment-card">
                <div class="payment-icon">💳</div>
                <h2 style="margin-bottom: 8px;">Complete Payment</h2>
                <p class="payment-course"><?php echo e($course['title']); ?></p>
                <div class="payment-amount">₹<?php echo number_format($course['price'], 0); ?></div>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="text-align: left;">⚠️ <?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="payment-form" style="text-align: left;">
                    <div class="form-group">
                        <label for="card_name">Cardholder Name</label>
                        <input type="text" id="card_name" name="card_name" placeholder="John Doe" required
                               style="text-align: left; letter-spacing: 0;">
                    </div>
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="4242 4242 4242 4242"
                               maxlength="19" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                    <button type="submit" name="pay_now" class="btn btn-success btn-block btn-lg" style="margin-top: 8px;">
                        🔒 Pay ₹<?php echo number_format($course['price'], 0); ?> Securely
                    </button>
                </form>

                <p style="margin-top: 16px; font-size: 0.8rem; color: var(--text-muted);">
                    🔒 This is a simulated payment. No real charges will be made.
                </p>
            </div>
        <?php endif; ?>
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
