<?php
/**
 * Reusable Utility Functions - EduShield
 */

require_once __DIR__ . '/db.php';

/**
 * Sanitize output to prevent XSS
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get all courses with optional filters
 */
function getCourses($filters = []) {
    global $conn;

    $sql = "SELECT c.*, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as review_count 
            FROM courses c 
            LEFT JOIN reviews r ON c.id = r.course_id";
    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($filters['category'])) {
        $conditions[] = "c.category = ?";
        $params[] = $filters['category'];
        $types .= "s";
    }
    if (!empty($filters['instructor'])) {
        $conditions[] = "c.instructor = ?";
        $params[] = $filters['instructor'];
        $types .= "s";
    }
    if (!empty($filters['search'])) {
        $conditions[] = "(c.title LIKE ? OR c.description LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }
    if (!empty($filters['max_price'])) {
        $conditions[] = "c.price <= ?";
        $params[] = $filters['max_price'];
        $types .= "d";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY c.id";

    if (!empty($filters['min_rating'])) {
        $sql .= " HAVING avg_rating >= ?";
        $params[] = $filters['min_rating'];
        $types .= "d";
    }

    $sql .= " ORDER BY c.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    $stmt->close();
    return $courses;
}

/**
 * Get a single course by ID
 */
function getCourseById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT c.*, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as review_count 
                            FROM courses c 
                            LEFT JOIN reviews r ON c.id = r.course_id 
                            WHERE c.id = ? GROUP BY c.id");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
    return $course;
}

/**
 * Check if user is enrolled in a course
 */
function isEnrolled($userId, $courseId) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();
    $stmt->store_result();
    $enrolled = $stmt->num_rows > 0;
    $stmt->close();
    return $enrolled;
}

/**
 * Enroll user in a course
 */
function enrollUser($userId, $courseId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $courseId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Process payment (simulated)
 */
function processPayment($userId, $courseId, $amount) {
    global $conn;
    $status = 'success';
    $stmt = $conn->prepare("INSERT INTO payments (user_id, course_id, amount, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $userId, $courseId, $amount, $status);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get user's enrolled courses
 */
function getUserCourses($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT c.*, e.enrolled_at, COALESCE(p.progress_percent, 0) as progress_percent,
                            COALESCE(AVG(r.rating), 0) as avg_rating
                            FROM enrollments e 
                            JOIN courses c ON e.course_id = c.id 
                            LEFT JOIN progress p ON p.user_id = e.user_id AND p.course_id = e.course_id
                            LEFT JOIN reviews r ON r.course_id = c.id
                            WHERE e.user_id = ?
                            GROUP BY c.id
                            ORDER BY e.enrolled_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    $stmt->close();
    return $courses;
}

/**
 * Get user progress for a course
 */
function getUserProgress($userId, $courseId) {
    global $conn;
    $stmt = $conn->prepare("SELECT progress_percent FROM progress WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['progress_percent'] : 0;
}

/**
 * Update user progress
 */
function updateProgress($userId, $courseId, $percent) {
    global $conn;
    $percent = max(0, min(100, (int)$percent));
    $stmt = $conn->prepare("INSERT INTO progress (user_id, course_id, progress_percent) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE progress_percent = ?");
    $stmt->bind_param("iiii", $userId, $courseId, $percent, $percent);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Submit or update review
 */
function submitReview($userId, $courseId, $rating, $comment) {
    global $conn;
    $rating = max(1, min(5, (int)$rating));
    $comment = trim($comment);
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, course_id, rating, comment) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE rating = ?, comment = ?");
    $stmt->bind_param("iiisis", $userId, $courseId, $rating, $comment, $rating, $comment);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get reviews for a course
 */
function getCourseReviews($courseId) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, u.name as user_name FROM reviews r 
                            JOIN users u ON r.user_id = u.id 
                            WHERE r.course_id = ? ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
    return $reviews;
}

/**
 * Get user payments
 */
function getUserPayments($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.title as course_title FROM payments p 
                            JOIN courses c ON p.course_id = c.id 
                            WHERE p.user_id = ? ORDER BY p.payment_date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
    return $payments;
}

/**
 * Get all unique categories
 */
function getCategories() {
    global $conn;
    $result = $conn->query("SELECT DISTINCT category FROM courses ORDER BY category");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    return $categories;
}

/**
 * Get all unique instructors
 */
function getInstructors() {
    global $conn;
    $result = $conn->query("SELECT DISTINCT instructor FROM courses ORDER BY instructor");
    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row['instructor'];
    }
    return $instructors;
}

/* ===== ADMIN FUNCTIONS ===== */

/**
 * Get total counts for admin dashboard
 */
function getAdminStats() {
    global $conn;
    $stats = [];

    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
    $stats['total_students'] = $result->fetch_assoc()['total'];

    $result = $conn->query("SELECT COUNT(*) as total FROM courses");
    $stats['total_courses'] = $result->fetch_assoc()['total'];

    $result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'success'");
    $stats['total_revenue'] = $result->fetch_assoc()['total'];

    $result = $conn->query("SELECT COUNT(*) as total FROM enrollments");
    $stats['total_enrollments'] = $result->fetch_assoc()['total'];

    return $stats;
}

/**
 * Get all users (for admin)
 */
function getAllUsers() {
    global $conn;
    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

/**
 * Get all payments (for admin)
 */
function getAllPayments() {
    global $conn;
    $result = $conn->query("SELECT p.*, u.name as user_name, c.title as course_title 
                            FROM payments p 
                            JOIN users u ON p.user_id = u.id 
                            JOIN courses c ON p.course_id = c.id 
                            ORDER BY p.payment_date DESC");
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    return $payments;
}

/**
 * Add a new course
 */
function addCourse($title, $description, $price, $category, $instructor, $thumbnail, $videoUrl) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO courses (title, description, price, category, instructor, thumbnail, video_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $title, $description, $price, $category, $instructor, $thumbnail, $videoUrl);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Update an existing course
 */
function updateCourse($id, $title, $description, $price, $category, $instructor, $thumbnail, $videoUrl) {
    global $conn;
    $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, price=?, category=?, instructor=?, thumbnail=?, video_url=? WHERE id=?");
    $stmt->bind_param("ssdssssi", $title, $description, $price, $category, $instructor, $thumbnail, $videoUrl, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Delete a course
 */
function deleteCourse($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get recent transactions (for admin dashboard)
 */
function getRecentTransactions($limit = 5) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, u.name as user_name, c.title as course_title 
                            FROM payments p 
                            JOIN users u ON p.user_id = u.id 
                            JOIN courses c ON p.course_id = c.id 
                            ORDER BY p.payment_date DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $stmt->close();
    return $transactions;
}

/**
 * Delete a user (admin only, cannot delete self)
 */
function deleteUser($userId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("i", $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get revenue report grouped by course
 */
function getCourseRevenueReport() {
    global $conn;
    $result = $conn->query("SELECT c.id, c.title, c.category, c.instructor, c.price,
                            COUNT(DISTINCT e.id) as total_enrollments,
                            COALESCE(SUM(p.amount), 0) as total_revenue,
                            COALESCE(AVG(r.rating), 0) as avg_rating
                            FROM courses c
                            LEFT JOIN enrollments e ON c.id = e.course_id
                            LEFT JOIN payments p ON c.id = p.course_id AND p.status = 'success'
                            LEFT JOIN reviews r ON c.id = r.course_id
                            GROUP BY c.id
                            ORDER BY total_revenue DESC");
    $report = [];
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
    return $report;
}

/**
 * Get monthly revenue report
 */
function getMonthlyRevenueReport() {
    global $conn;
    $result = $conn->query("SELECT 
                            DATE_FORMAT(payment_date, '%Y-%m') as month,
                            DATE_FORMAT(payment_date, '%b %Y') as month_label,
                            COUNT(*) as total_transactions,
                            SUM(amount) as total_revenue
                            FROM payments 
                            WHERE status = 'success'
                            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                            ORDER BY month DESC
                            LIMIT 12");
    $report = [];
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
    return $report;
}

/**
 * Get category-wise revenue
 */
function getCategoryRevenueReport() {
    global $conn;
    $result = $conn->query("SELECT c.category,
                            COUNT(DISTINCT e.id) as total_enrollments,
                            COALESCE(SUM(p.amount), 0) as total_revenue
                            FROM courses c
                            LEFT JOIN enrollments e ON c.id = e.course_id
                            LEFT JOIN payments p ON c.id = p.course_id AND p.status = 'success'
                            GROUP BY c.category
                            ORDER BY total_revenue DESC");
    $report = [];
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
    return $report;
}

/**
 * Get top-performing instructors
 */
function getInstructorReport() {
    global $conn;
    $result = $conn->query("SELECT c.instructor,
                            COUNT(DISTINCT c.id) as total_courses,
                            COUNT(DISTINCT e.id) as total_enrollments,
                            COALESCE(SUM(p.amount), 0) as total_revenue,
                            COALESCE(AVG(r.rating), 0) as avg_rating
                            FROM courses c
                            LEFT JOIN enrollments e ON c.id = e.course_id
                            LEFT JOIN payments p ON c.id = p.course_id AND p.status = 'success'
                            LEFT JOIN reviews r ON c.id = r.course_id
                            GROUP BY c.instructor
                            ORDER BY total_revenue DESC");
    $report = [];
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
    return $report;
}

