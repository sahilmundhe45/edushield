<?php
/**
 * EduShield – Admin: Transactions
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$payments = getAllPayments();
$totalRevenue = array_sum(array_column(array_filter($payments, function($p) { return $p['status'] === 'success'; }), 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions – EduShield Admin</title>
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
            <a href="/edushield/admin/manage_courses.php">Courses</a>
            <a href="/edushield/admin/manage_users.php">Users</a>
            <a href="/edushield/admin/transactions.php" class="active">Transactions</a>
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
                <h1>💳 Transactions</h1>
                <p>View all payment transactions on the platform</p>
            </div>

            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">₹<?php echo number_format($totalRevenue, 0); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo count($payments); ?></div>
                    <div class="stat-label">Total Transactions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value"><?php echo count(array_filter($payments, function($p){ return $p['status'] === 'success'; })); ?></div>
                    <div class="stat-label">Successful</div>
                </div>
            </div>

            <div class="dash-section">
                <h3 class="dash-section-title">📋 All Transactions</h3>
                <?php if (empty($payments)): ?>
                    <div class="empty-state"><p>No transactions yet.</p></div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $p): ?>
                                    <tr>
                                        <td>#<?php echo $p['id']; ?></td>
                                        <td><?php echo e($p['user_name']); ?></td>
                                        <td><?php echo e($p['course_title']); ?></td>
                                        <td>₹<?php echo number_format($p['amount'], 0); ?></td>
                                        <td>
                                            <span class="badge <?php
                                                echo $p['status'] === 'success' ? 'badge-success' :
                                                    ($p['status'] === 'pending' ? 'badge-warning' : 'badge-danger');
                                            ?>">
                                                <?php echo ucfirst($p['status']); ?>
                                            </span>
                                        </td>
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
