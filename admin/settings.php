<?php
/**
 * EduShield – Admin: Platform Security Settings
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$messageType = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'All password fields are required.';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'New password must be at least 6 characters.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    } else {
        global $conn;
        $userId = getCurrentUserId();
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();
            $message = 'Password changed successfully!';
            $messageType = 'success';
        } else {
            $message = 'Current password is incorrect.';
            $messageType = 'error';
        }
    }
}

$stats = getAdminStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Settings – EduShield Admin</title>
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
            <a href="/edushield/admin/reports.php">Reports</a>
            <a href="/edushield/admin/settings.php" class="active">Settings</a>
            <a href="/edushield/logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<main>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>⚙️ Platform Security Settings</h1>
                <p>Manage security configurations and admin account settings</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo ($messageType === 'success') ? '✅' : '⚠️'; ?> <?php echo e($message); ?>
                </div>
            <?php endif; ?>

            <!-- Security Overview -->
            <div class="dash-section">
                <h3 class="dash-section-title">🔒 Security Status</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🛡️</div>
                        <div class="stat-value" style="color: var(--success); font-size: 1.2rem;">Active</div>
                        <div class="stat-label">SQL Injection Protection</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔐</div>
                        <div class="stat-value" style="color: var(--success); font-size: 1.2rem;">Active</div>
                        <div class="stat-label">XSS Prevention</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔑</div>
                        <div class="stat-value" style="color: var(--success); font-size: 1.2rem;">bcrypt</div>
                        <div class="stat-label">Password Hashing</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👮</div>
                        <div class="stat-value" style="color: var(--success); font-size: 1.2rem;">Active</div>
                        <div class="stat-label">Role-Based Access</div>
                    </div>
                </div>
            </div>

            <!-- Security Features Info -->
            <div class="dash-section">
                <h3 class="dash-section-title">📋 Security Features Implemented</h3>
                <div class="admin-form" style="margin-bottom: 0;">
                    <div class="table-container" style="border: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SQL Injection Prevention</td>
                                    <td>Prepared Statements (MySQLi)</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Cross-Site Scripting (XSS)</td>
                                    <td>htmlspecialchars() on all outputs</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Password Security</td>
                                    <td>password_hash() + password_verify()</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Session Management</td>
                                    <td>PHP Sessions with role verification</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Course Access Control</td>
                                    <td>Enrollment verification before content</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Admin Page Protection</td>
                                    <td>requireAdmin() on all admin pages</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Student Page Protection</td>
                                    <td>requireStudent() on student pages</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                                <tr>
                                    <td>Input Validation</td>
                                    <td>Server-side validation on all forms</td>
                                    <td><span class="badge badge-success">✅ Active</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Change Admin Password -->
            <div class="dash-section">
                <h3 class="dash-section-title">🔑 Change Admin Password</h3>
                <div class="admin-form">
                    <form method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   placeholder="Enter current password" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password"
                                       placeholder="Min 6 characters" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password"
                                       placeholder="Re-enter password" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            🔒 Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Platform Info -->
            <div class="dash-section">
                <h3 class="dash-section-title">ℹ️ Platform Information</h3>
                <div class="admin-form" style="margin-bottom: 0;">
                    <div class="table-container" style="border: none;">
                        <table class="data-table">
                            <tbody>
                                <tr><td style="color:var(--text-muted)">Platform</td><td>EduShield v1.0</td></tr>
                                <tr><td style="color:var(--text-muted)">PHP Version</td><td><?php echo phpversion(); ?></td></tr>
                                <tr><td style="color:var(--text-muted)">Server</td><td><?php echo e($_SERVER['SERVER_SOFTWARE'] ?? 'Apache/XAMPP'); ?></td></tr>
                                <tr><td style="color:var(--text-muted)">Database</td><td>MySQL (edushield)</td></tr>
                                <tr><td style="color:var(--text-muted)">Admin Email</td><td><?php echo e($_SESSION['user_email'] ?? ''); ?></td></tr>
                                <tr><td style="color:var(--text-muted)">Registered Users</td><td><?php echo $stats['total_students'] + 1; ?></td></tr>
                                <tr><td style="color:var(--text-muted)">Total Courses</td><td><?php echo $stats['total_courses']; ?></td></tr>
                            </tbody>
                        </table>
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

<script src="../js/scripts.js"></script>
</body>
</html>
