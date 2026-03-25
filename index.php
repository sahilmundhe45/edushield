<?php
/**
 * EduShield – Homepage
 */
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Get filters
$filters = [];
if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
if (!empty($_GET['instructor'])) $filters['instructor'] = $_GET['instructor'];
if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
if (!empty($_GET['min_rating'])) $filters['min_rating'] = $_GET['min_rating'];

$courses = getCourses($filters);
$categories = getCategories();
$instructors = getInstructors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduShield – A secure online learning platform offering quality courses in Programming, Data Science, Cybersecurity, and more.">
    <title>EduShield – Secure Online Learning Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo">
            <span>🛡️</span>
            <span>EduShield</span>
        </a>
        <div class="hamburger">
            <span></span><span></span><span></span>
        </div>
        <div class="nav-links">
            <a href="/edushield/" class="active">Home</a>
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
    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Learn Without <span>Limits</span></h1>
            <p>Unlock your potential with expert-led courses. Secure, affordable, and designed for your success.</p>
            <a href="#courses" class="btn btn-primary btn-lg">Explore Courses</a>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="section" id="courses">
        <div class="container">
            <div class="section-title">
                <h2>📚 Our Courses</h2>
                <p>Browse our curated collection of high-quality courses</p>
            </div>

            <!-- Alerts -->
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-error">⚠️ <?php echo e($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success">✅ <?php echo e($_GET['success']); ?></div>
            <?php endif; ?>

            <!-- Filter Bar -->
            <form class="filter-bar" id="filterForm" method="GET">
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" placeholder="Search courses..." value="<?php echo e($filters['search'] ?? ''); ?>">
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo (($filters['category'] ?? '') === $cat) ? 'selected' : ''; ?>>
                                <?php echo e($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Instructor</label>
                    <select name="instructor">
                        <option value="">All Instructors</option>
                        <?php foreach ($instructors as $inst): ?>
                            <option value="<?php echo e($inst); ?>" <?php echo (($filters['instructor'] ?? '') === $inst) ? 'selected' : ''; ?>>
                                <?php echo e($inst); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Max Price (₹)</label>
                    <input type="number" name="max_price" placeholder="e.g. 1000" value="<?php echo e($filters['max_price'] ?? ''); ?>">
                </div>
                <div class="filter-group">
                    <label>Min Rating</label>
                    <select name="min_rating">
                        <option value="">Any Rating</option>
                        <option value="4" <?php echo (($filters['min_rating'] ?? '') == '4') ? 'selected' : ''; ?>>4★ & above</option>
                        <option value="3" <?php echo (($filters['min_rating'] ?? '') == '3') ? 'selected' : ''; ?>>3★ & above</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <!-- Course Grid -->
            <?php if (empty($courses)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>No courses found</h3>
                    <p>Try adjusting your filters or search terms.</p>
                </div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <a href="/edushield/course.php?id=<?php echo $course['id']; ?>" class="course-card">
                            <div class="card-thumb">📘</div>
                            <div class="card-body">
                                <span class="card-category"><?php echo e($course['category']); ?></span>
                                <h3 class="card-title"><?php echo e($course['title']); ?></h3>
                                <p class="card-instructor">👨‍🏫 <?php echo e($course['instructor']); ?></p>
                                <div class="card-footer">
                                    <span class="card-price">₹<?php echo number_format($course['price'], 0); ?></span>
                                    <span class="card-rating">
                                        ★ <?php echo number_format($course['avg_rating'], 1); ?>
                                        <span style="color:var(--text-muted)">(<?php echo $course['review_count']; ?>)</span>
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

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>🛡️ EduShield</h4>
                <p>A secure online learning platform built with security-first principles. Learn from industry experts with confidence.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/edushield/">Home</a></li>
                    <li><a href="/edushield/login.php">Login</a></li>
                    <li><a href="/edushield/register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    <li><a href="/edushield/?category=Programming">Programming</a></li>
                    <li><a href="/edushield/?category=Data+Science">Data Science</a></li>
                    <li><a href="/edushield/?category=Cybersecurity">Cybersecurity</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li>📧 support@edushield.com</li>
                    <li>📞 +91 98765 43210</li>
                    <li>📍 New Delhi, India</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EduShield. All rights reserved. Built with ❤️ for secure learning.</p>
        </div>
    </div>
</footer>

<script src="js/scripts.js"></script>
</body>
</html>
