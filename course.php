<?php
/**
 * EduShield – Course Detail Page
 */
require_once 'includes/session.php';
require_once 'includes/functions.php';

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = getCourseById($courseId);

if (!$course) {
    header("Location: /edushield/?error=" . urlencode("Course not found."));
    exit();
}

$reviews = getCourseReviews($courseId);
$enrolled = isLoggedIn() ? isEnrolled(getCurrentUserId(), $courseId) : false;
$userProgress = ($enrolled && isLoggedIn()) ? getUserProgress(getCurrentUserId(), $courseId) : 0;

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        header("Location: /edushield/login.php?error=" . urlencode("Please login to submit a review."));
        exit();
    }
    if (!$enrolled) {
        $reviewError = "You must be enrolled to review this course.";
    } else {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = $_POST['comment'] ?? '';
        if ($rating < 1 || $rating > 5) {
            $reviewError = "Please select a valid rating (1-5).";
        } else {
            submitReview(getCurrentUserId(), $courseId, $rating, $comment);
            header("Location: /edushield/course.php?id=$courseId&success=" . urlencode("Review submitted!"));
            exit();
        }
    }
}

// Handle progress update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_progress'])) {
    if (isLoggedIn() && $enrolled) {
        $percent = (int)($_POST['progress_percent'] ?? 0);
        updateProgress(getCurrentUserId(), $courseId, $percent);
        header("Location: /edushield/course.php?id=$courseId&success=" . urlencode("Progress updated!"));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($course['title']); ?> – EduShield</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo">
            <span>🛡️</span>
            <span>EduShield</span>
        </a>
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <?php if (isLoggedIn()): ?>
                <?php if (getCurrentUserRole() === 'admin'): ?>
                    <a href="/edushield/admin/dashboard.php">Admin Panel</a>
                <?php else: ?>
                    <a href="/edushield/student/dashboard.php">Dashboard</a>
                    <a href="/edushield/student/my_courses.php">My Courses</a>
                <?php endif; ?>
                <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
            <?php else: ?>
                <a href="/edushield/login.php">Login</a>
                <a href="/edushield/register.php" class="btn btn-primary btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <section class="course-detail">
        <div class="container">

            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success">✅ <?php echo e($_GET['success']); ?></div>
            <?php endif; ?>
            <?php if (!empty($reviewError)): ?>
                <div class="alert alert-error">⚠️ <?php echo e($reviewError); ?></div>
            <?php endif; ?>

            <div class="course-detail-grid">
                <!-- Main Content -->
                <div class="course-main">
                    <div class="course-thumb">
                        <?php if ($enrolled && !empty($course['video_url'])): ?>
                            <iframe src="<?php echo e($course['video_url']); ?>" allowfullscreen></iframe>
                        <?php else: ?>
                            📘
                        <?php endif; ?>
                    </div>

                    <h1><?php echo e($course['title']); ?></h1>

                    <div class="course-meta">
                        <span>👨‍🏫 <?php echo e($course['instructor']); ?></span>
                        <span>📂 <?php echo e($course['category']); ?></span>
                        <span>⭐ <?php echo number_format($course['avg_rating'], 1); ?> (<?php echo $course['review_count']; ?> reviews)</span>
                    </div>

                    <div class="course-description">
                        <h3>About This Course</h3>
                        <p><?php echo nl2br(e($course['description'])); ?></p>
                    </div>

                    <?php if ($enrolled): ?>
                        <!-- Progress Section -->
                        <div class="dash-section">
                            <h3 class="dash-section-title">📊 Your Progress</h3>
                            <div class="progress-info" style="margin-bottom: 12px;">
                                <div class="progress-bar-container" style="height: 12px;">
                                    <div class="progress-bar" data-progress="<?php echo $userProgress; ?>"></div>
                                </div>
                                <span class="progress-percent"><?php echo $userProgress; ?>%</span>
                            </div>
                            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                                <input type="number" name="progress_percent" min="0" max="100" value="<?php echo $userProgress; ?>"
                                       style="width: 100px; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); color: var(--text-primary); font-family: var(--font);">
                                <button type="submit" name="update_progress" class="btn btn-secondary btn-sm">Update</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Reviews -->
                    <div class="reviews-section">
                        <h3 class="dash-section-title">💬 Reviews (<?php echo count($reviews); ?>)</h3>

                        <?php if ($enrolled && isLoggedIn()): ?>
                            <form method="POST" class="admin-form" style="margin-bottom: 20px;">
                                <h3>Write a Review</h3>
                                <div class="form-group">
                                    <label>Rating</label>
                                    <div class="star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>">
                                            <label for="star<?php echo $i; ?>">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Your Review</label>
                                    <textarea id="comment" name="comment" placeholder="Share your experience..." rows="3"></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            </form>
                        <?php endif; ?>

                        <?php if (empty($reviews)): ?>
                            <div class="empty-state" style="padding: 30px;">
                                <p>No reviews yet. Be the first to review!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="review-author"><?php echo e($review['user_name']); ?></span>
                                        <span class="review-rating">
                                            <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                        </span>
                                    </div>
                                    <p class="review-text"><?php echo e($review['comment']); ?></p>
                                    <p class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="course-sidebar">
                    <div class="sidebar-card">
                        <div class="price-tag">₹<?php echo number_format($course['price'], 0); ?> <small>INR</small></div>

                        <ul class="sidebar-info">
                            <li><span>Category</span><span><?php echo e($course['category']); ?></span></li>
                            <li><span>Instructor</span><span><?php echo e($course['instructor']); ?></span></li>
                            <li><span>Rating</span><span>⭐ <?php echo number_format($course['avg_rating'], 1); ?></span></li>
                            <li><span>Reviews</span><span><?php echo $course['review_count']; ?></span></li>
                        </ul>

                        <?php if ($enrolled): ?>
                            <div class="alert alert-success" style="margin-bottom: 0;">✅ You are enrolled in this course</div>
                        <?php elseif (isLoggedIn() && getCurrentUserRole() === 'student'): ?>
                            <a href="/edushield/enroll.php?id=<?php echo $course['id']; ?>" class="btn btn-success btn-block btn-lg">
                                Enroll Now – ₹<?php echo number_format($course['price'], 0); ?>
                            </a>
                        <?php elseif (!isLoggedIn()): ?>
                            <a href="/edushield/login.php?error=<?php echo urlencode('Please login to enroll.'); ?>" class="btn btn-primary btn-block btn-lg">
                                Login to Enroll
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
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

<script src="js/scripts.js"></script>
</body>
</html>
