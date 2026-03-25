<?php
/**
 * EduShield – Admin Dashboard
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$stats = getAdminStats();
$recentTransactions = getRecentTransactions(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – EduShield</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/edushield/" class="logo"><span>🛡️</span><span>EduShield</span></a>
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="nav-links">
            <a href="/edushield/">Home</a>
            <a href="/edushield/admin/dashboard.php" class="active">Dashboard</a>
            <a href="/edushield/admin/manage_courses.php">Courses</a>
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
                <h1>Admin Dashboard 🔧</h1>
                <p>Platform overview and management</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
                    <div class="stat-label">Total Courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 0); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo $stats['total_enrollments']; ?></div>
                    <div class="stat-label">Total Enrollments</div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="dash-section">
                <h3 class="dash-section-title">⚡ Quick Actions</h3>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="/edushield/admin/manage_courses.php" class="btn btn-primary">➕ Add New Course</a>
                    <a href="/edushield/admin/manage_users.php" class="btn btn-secondary">👥 Manage Users</a>
                    <a href="/edushield/admin/transactions.php" class="btn btn-secondary">💳 View Transactions</a>
                    <a href="/edushield/admin/reports.php" class="btn btn-secondary">📊 View Reports</a>
                    <a href="/edushield/admin/settings.php" class="btn btn-secondary">⚙️ Settings</a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="dash-section">
                <h3 class="dash-section-title">
                    💳 Recent Transactions
                    <a href="/edushield/admin/transactions.php" class="btn btn-secondary btn-sm">View All</a>
                </h3>
                <?php if (empty($recentTransactions)): ?>
                    <div class="empty-state"><p>No transactions yet.</p></div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $t): ?>
                                    <tr>
                                        <td><?php echo e($t['user_name']); ?></td>
                                        <td><?php echo e($t['course_title']); ?></td>
                                        <td>₹<?php echo number_format($t['amount'], 0); ?></td>
                                        <td><span class="badge badge-success"><?php echo e($t['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($t['payment_date'])); ?></td>
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
