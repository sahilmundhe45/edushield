<?php
/**
 * EduShield – Admin: Manage Courses
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$messageType = '';

// Handle Add Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $thumbnail = trim($_POST['thumbnail'] ?? 'default_course.png');
    $videoUrl = trim($_POST['video_url'] ?? '');

    if (empty($title) || empty($description) || empty($category) || empty($instructor)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        if (addCourse($title, $description, $price, $category, $instructor, $thumbnail, $videoUrl)) {
            $message = 'Course added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add course.';
            $messageType = 'error';
        }
    }
}

// Handle Edit Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_course'])) {
    $id = (int)$_POST['course_id'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $thumbnail = trim($_POST['thumbnail'] ?? 'default_course.png');
    $videoUrl = trim($_POST['video_url'] ?? '');

    if (updateCourse($id, $title, $description, $price, $category, $instructor, $thumbnail, $videoUrl)) {
        $message = 'Course updated successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to update course.';
        $messageType = 'error';
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if (deleteCourse($deleteId)) {
        header("Location: /edushield/admin/manage_courses.php?msg=" . urlencode("Course deleted.") . "&type=success");
        exit();
    }
}

if (!empty($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'success';
}

// Get all courses
$courses = getCourses();

// Get course to edit
$editCourse = null;
if (isset($_GET['edit'])) {
    $editCourse = getCourseById((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses – EduShield Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo"><span>🛡️</span><span>EduShield</span></a>
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <a href="/edushield/admin/dashboard.php">Dashboard</a>
            <a href="/edushield/admin/manage_courses.php" class="active">Courses</a>
            <a href="/edushield/admin/manage_users.php">Users</a>
            <a href="/edushield/admin/transactions.php">Transactions</a>
            <a href="/edushield/admin/reports.php">Reports</a>
            <a href="/edushield/admin/settings.php">Settings</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>📚 Manage Courses</h1>
                <p>Add, edit, or delete courses on the platform</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo ($messageType === 'success') ? '✅' : '⚠️'; ?> <?php echo e($message); ?>
                </div>
            <?php endif; ?>

            <!-- Add / Edit Course Form -->
            <div class="admin-form">
                <h3><?php echo $editCourse ? '✏️ Edit Course' : '➕ Add New Course'; ?></h3>
                <form method="POST">
                    <?php if ($editCourse): ?>
                        <input type="hidden" name="course_id" value="<?php echo $editCourse['id']; ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Course Title *</label>
                            <input type="text" name="title" required
                                   value="<?php echo e($editCourse['title'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <input type="text" name="category" required placeholder="e.g. Programming"
                                   value="<?php echo e($editCourse['category'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" rows="3" required><?php echo e($editCourse['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" step="0.01" min="0" required
                                   value="<?php echo e($editCourse['price'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Instructor *</label>
                            <input type="text" name="instructor" required
                                   value="<?php echo e($editCourse['instructor'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Thumbnail Filename</label>
                            <input type="text" name="thumbnail" placeholder="default_course.png"
                                   value="<?php echo e($editCourse['thumbnail'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Video URL (embed)</label>
                            <input type="text" name="video_url" placeholder="https://www.youtube.com/embed/..."
                                   value="<?php echo e($editCourse['video_url'] ?? ''); ?>">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 8px;">
                        <button type="submit" name="<?php echo $editCourse ? 'edit_course' : 'add_course'; ?>"
                                class="btn btn-primary">
                            <?php echo $editCourse ? '💾 Update Course' : '➕ Add Course'; ?>
                        </button>
                        <?php if ($editCourse): ?>
                            <a href="/edushield/admin/manage_courses.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Course List -->
            <div class="dash-section">
                <h3 class="dash-section-title">📋 All Courses (<?php echo count($courses); ?>)</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Instructor</th>
                                <th>Price</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td><?php echo $c['id']; ?></td>
                                    <td><?php echo e($c['title']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo e($c['category']); ?></span></td>
                                    <td><?php echo e($c['instructor']); ?></td>
                                    <td>₹<?php echo number_format($c['price'], 0); ?></td>
                                    <td>⭐ <?php echo number_format($c['avg_rating'], 1); ?></td>
                                    <td>
                                        <a href="/edushield/admin/manage_courses.php?edit=<?php echo $c['id']; ?>"
                                           class="btn btn-secondary btn-sm">✏️ Edit</a>
                                        <a href="/edushield/admin/manage_courses.php?delete=<?php echo $c['id']; ?>"
                                           class="btn btn-danger btn-sm confirm-delete">🗑️ Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

<script src="../js/scripts.js"></script>
</body>
</html>
