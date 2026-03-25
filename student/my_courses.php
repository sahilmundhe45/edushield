<?php
/**
 * EduShield – Student: My Courses
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireStudent();

$userId = getCurrentUserId();
$myCourses = getUserCourses($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses – EduShield</title>
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
            <a href="/edushield/student/my_courses.php" class="active">My Courses</a>
            <a href="/edushield/student/progress.php">Progress</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>📚 My Courses</h1>
                <p>All courses you are currently enrolled in</p>
            </div>

            <?php if (empty($myCourses)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📖</div>
                    <h3>No courses yet</h3>
                    <p>Start exploring and enroll in courses that interest you!</p>
                    <a href="/edushield/" class="btn btn-primary" style="margin-top:16px;">Browse Courses</a>
                </div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($myCourses as $course): ?>
                        <a href="/edushield/course.php?id=<?php echo $course['id']; ?>" class="course-card">
                            <div class="card-thumb">📘</div>
                            <div class="card-body">
                                <span class="card-category"><?php echo e($course['category']); ?></span>
                                <h3 class="card-title"><?php echo e($course['title']); ?></h3>
                                <p class="card-instructor">👨‍🏫 <?php echo e($course['instructor']); ?></p>
                                <div style="margin-top: 8px;">
                                    <div class="progress-info">
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" data-progress="<?php echo $course['progress_percent']; ?>"></div>
                                        </div>
                                        <span class="progress-percent"><?php echo $course['progress_percent']; ?>%</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <span class="card-rating">⭐ <?php echo number_format($course['avg_rating'], 1); ?></span>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">
                                        Enrolled <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
