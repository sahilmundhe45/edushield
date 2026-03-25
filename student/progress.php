<?php
/**
 * EduShield – Student: Progress Tracker
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireStudent();

$userId = getCurrentUserId();
$myCourses = getUserCourses($userId);
$payments = getUserPayments($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress – EduShield</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo"><span>🛡️</span><span>EduShield</span></a>
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <a href="/edushield/student/dashboard.php">Dashboard</a>
            <a href="/edushield/student/my_courses.php">My Courses</a>
            <a href="/edushield/student/progress.php" class="active">Progress</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>📊 Learning Progress</h1>
                <p>Track your progress across all enrolled courses</p>
            </div>

            <!-- Progress Overview -->
            <div class="dash-section">
                <h3 class="dash-section-title">📈 Course Progress</h3>

                <?php if (empty($myCourses)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <h3>No progress to show</h3>
                        <p>Enroll in courses to start tracking.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($myCourses as $course): ?>
                        <div class="enrolled-card">
                            <div class="enrolled-thumb">
                                <?php if ($course['progress_percent'] >= 100): ?>
                                    🏆
                                <?php elseif ($course['progress_percent'] >= 50): ?>
                                    📗
                                <?php else: ?>
                                    📘
                                <?php endif; ?>
                            </div>
                            <div class="enrolled-info" style="flex: 1;">
                                <h4>
                                    <?php echo e($course['title']); ?>
                                    <?php if ($course['progress_percent'] >= 100): ?>
                                        <span class="badge badge-success" style="margin-left: 8px;">Completed</span>
                                    <?php endif; ?>
                                </h4>
                                <p class="enrolled-instructor">👨‍🏫 <?php echo e($course['instructor']); ?></p>
                                <div class="progress-info">
                                    <div class="progress-bar-container" style="height: 12px;">
                                        <div class="progress-bar" data-progress="<?php echo $course['progress_percent']; ?>"></div>
                                    </div>
                                    <span class="progress-percent"><?php echo $course['progress_percent']; ?>%</span>
                                </div>
                            </div>
                            <a href="/edushield/course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">
                                <?php echo ($course['progress_percent'] >= 100) ? 'Review' : 'Continue'; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Payment History -->
            <div class="dash-section">
                <h3 class="dash-section-title">💳 Full Payment History</h3>
                <?php if (empty($payments)): ?>
                    <div class="empty-state"><p>No payments yet.</p></div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $i => $p): ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo e($p['course_title']); ?></td>
                                        <td>₹<?php echo number_format($p['amount'], 0); ?></td>
                                        <td><span class="badge badge-success"><?php echo e($p['status']); ?></span></td>
                                        <td><?php echo date('M d, Y – h:i A', strtotime($p['payment_date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EduShield. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="../js/scripts.js"></script>
</body>
</html>
