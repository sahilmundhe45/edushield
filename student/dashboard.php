<?php
/**
 * EduShield – Student Dashboard
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireStudent();

$userId = getCurrentUserId();
$myCourses = getUserCourses($userId);
$payments = getUserPayments($userId);

$totalCourses = count($myCourses);
$totalSpent = array_sum(array_column($payments, 'amount'));
$avgProgress = $totalCourses > 0 ? round(array_sum(array_column($myCourses, 'progress_percent')) / $totalCourses) : 0;
$completed = count(array_filter($myCourses, function($c) { return $c['progress_percent'] >= 100; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard – EduShield</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo"><span>🛡️</span><span>EduShield</span></a>
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <a href="/edushield/student/dashboard.php" class="active">Dashboard</a>
            <a href="/edushield/student/my_courses.php">My Courses</a>
            <a href="/edushield/student/progress.php">Progress</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome back, <?php echo e(getCurrentUserName()); ?> 👋</h1>
                <p>Here's an overview of your learning journey</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?php echo $totalCourses; ?></div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-value"><?php echo $avgProgress; ?>%</div>
                    <div class="stat-label">Avg Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value"><?php echo $completed; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">₹<?php echo number_format($totalSpent, 0); ?></div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>

            <!-- Continue Learning -->
            <div class="dash-section">
                <h3 class="dash-section-title">
                    🎯 Continue Learning
                    <a href="/edushield/student/my_courses.php" class="btn btn-secondary btn-sm">View All</a>
                </h3>

                <?php
                $inProgress = array_filter($myCourses, function($c) { return $c['progress_percent'] < 100; });
                $inProgress = array_slice($inProgress, 0, 3);
                ?>

                <?php if (empty($inProgress)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📖</div>
                        <h3>No courses in progress</h3>
                        <p><a href="/edushield/" class="btn btn-primary" style="margin-top:12px;">Browse Courses</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($inProgress as $course): ?>
                        <a href="/edushield/course.php?id=<?php echo $course['id']; ?>" class="enrolled-card">
                            <div class="enrolled-thumb">📘</div>
                            <div class="enrolled-info">
                                <h4><?php echo e($course['title']); ?></h4>
                                <p class="enrolled-instructor">👨‍🏫 <?php echo e($course['instructor']); ?></p>
                                <div class="progress-info">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" data-progress="<?php echo $course['progress_percent']; ?>"></div>
                                    </div>
                                    <span class="progress-percent"><?php echo $course['progress_percent']; ?>%</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Payment History -->
            <div class="dash-section">
                <h3 class="dash-section-title">💳 Recent Payments</h3>
                <?php if (empty($payments)): ?>
                    <div class="empty-state"><p>No payments yet.</p></div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($payments, 0, 5) as $p): ?>
                                    <tr>
                                        <td><?php echo e($p['course_title']); ?></td>
                                        <td>₹<?php echo number_format($p['amount'], 0); ?></td>
                                        <td><span class="badge badge-success"><?php echo e($p['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($p['payment_date'])); ?></td>
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
