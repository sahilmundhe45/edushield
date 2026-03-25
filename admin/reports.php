<?php
/**
 * EduShield – Admin: Reports & Analytics
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$stats = getAdminStats();
$courseReport = getCourseRevenueReport();
$monthlyReport = getMonthlyRevenueReport();
$categoryReport = getCategoryRevenueReport();
$instructorReport = getInstructorReport();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics – EduShield Admin</title>
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
            <a href="/edushield/admin/transactions.php">Transactions</a>
            <a href="/edushield/admin/reports.php" class="active">Reports</a>
            <a href="/edushield/admin/settings.php">Settings</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>📊 Reports & Analytics</h1>
                <p>Comprehensive insights into platform performance, revenue, and engagement</p>
            </div>

            <!-- Summary Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 0); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
                    <div class="stat-label">Total Courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo $stats['total_enrollments']; ?></div>
                    <div class="stat-label">Total Enrollments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <!-- Revenue by Course -->
            <div class="dash-section">
                <h3 class="dash-section-title">📈 Revenue by Course</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course Title</th>
                                <th>Category</th>
                                <th>Instructor</th>
                                <th>Price</th>
                                <th>Enrollments</th>
                                <th>Revenue</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courseReport as $i => $c): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td><?php echo e($c['title']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo e($c['category']); ?></span></td>
                                    <td><?php echo e($c['instructor']); ?></td>
                                    <td>₹<?php echo number_format($c['price'], 0); ?></td>
                                    <td><?php echo $c['total_enrollments']; ?></td>
                                    <td style="font-weight:700; color: var(--success);">₹<?php echo number_format($c['total_revenue'], 0); ?></td>
                                    <td>⭐ <?php echo number_format($c['avg_rating'], 1); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: 700; border-top: 2px solid var(--primary);">
                                <td colspan="5">Total</td>
                                <td><?php echo array_sum(array_column($courseReport, 'total_enrollments')); ?></td>
                                <td style="color: var(--success);">₹<?php echo number_format(array_sum(array_column($courseReport, 'total_revenue')), 0); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Revenue by Category -->
            <div class="dash-section">
                <h3 class="dash-section-title">📂 Revenue by Category</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Enrollments</th>
                                <th>Revenue</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalCatRevenue = array_sum(array_column($categoryReport, 'total_revenue'));
                            foreach ($categoryReport as $cat):
                                $share = $totalCatRevenue > 0 ? ($cat['total_revenue'] / $totalCatRevenue) * 100 : 0;
                            ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo e($cat['category']); ?></span></td>
                                    <td><?php echo $cat['total_enrollments']; ?></td>
                                    <td style="font-weight:700; color: var(--success);">₹<?php echo number_format($cat['total_revenue'], 0); ?></td>
                                    <td>
                                        <div class="progress-info">
                                            <div class="progress-bar-container" style="width: 120px;">
                                                <div class="progress-bar" data-progress="<?php echo round($share); ?>"></div>
                                            </div>
                                            <span class="progress-percent"><?php echo round($share); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue by Instructor -->
            <div class="dash-section">
                <h3 class="dash-section-title">👨‍🏫 Instructor Performance</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Courses</th>
                                <th>Enrollments</th>
                                <th>Revenue</th>
                                <th>Avg Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instructorReport as $inst): ?>
                                <tr>
                                    <td><?php echo e($inst['instructor']); ?></td>
                                    <td><?php echo $inst['total_courses']; ?></td>
                                    <td><?php echo $inst['total_enrollments']; ?></td>
                                    <td style="font-weight:700; color: var(--success);">₹<?php echo number_format($inst['total_revenue'], 0); ?></td>
                                    <td>⭐ <?php echo number_format($inst['avg_rating'], 1); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="dash-section">
                <h3 class="dash-section-title">📅 Monthly Revenue</h3>
                <?php if (empty($monthlyReport)): ?>
                    <div class="empty-state"><p>No monthly data available yet.</p></div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Transactions</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyReport as $m): ?>
                                    <tr>
                                        <td><?php echo e($m['month_label']); ?></td>
                                        <td><?php echo $m['total_transactions']; ?></td>
                                        <td style="font-weight:700; color: var(--success);">₹<?php echo number_format($m['total_revenue'], 0); ?></td>
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
