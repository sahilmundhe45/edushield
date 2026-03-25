<?php
/**
 * EduShield – Admin: Manage Users
 */
require_once '../includes/session.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$messageType = '';

// Handle Delete User
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    // Prevent admin from deleting themselves
    if ($deleteId === getCurrentUserId()) {
        $message = 'You cannot delete your own account.';
        $messageType = 'error';
    } else {
        if (deleteUser($deleteId)) {
            header("Location: /edushield/admin/manage_users.php?msg=" . urlencode("User deleted successfully.") . "&type=success");
            exit();
        } else {
            $message = 'Failed to delete user. Admin accounts cannot be deleted.';
            $messageType = 'error';
        }
    }
}

if (!empty($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'success';
}

$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users – EduShield Admin</title>
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
            <a href="/edushield/admin/manage_users.php" class="active">Users</a>
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
                <h1>👥 Manage Users</h1>
                <p>View, manage, and delete registered users on the platform</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo ($messageType === 'success') ? '✅' : '⚠️'; ?> <?php echo e($message); ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo count($users); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-value"><?php echo count(array_filter($users, function($u){ return $u['role'] === 'student'; })); ?></div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔧</div>
                    <div class="stat-value"><?php echo count(array_filter($users, function($u){ return $u['role'] === 'admin'; })); ?></div>
                    <div class="stat-label">Admins</div>
                </div>
            </div>

            <div class="dash-section">
                <h3 class="dash-section-title">📋 All Users</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo e($user['name']); ?></td>
                                    <td><?php echo e($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-warning' : 'badge-primary'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="/edushield/admin/manage_users.php?delete=<?php echo $user['id']; ?>"
                                               class="btn btn-danger btn-sm confirm-delete">🗑️ Delete</a>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.8rem;">Protected</span>
                                        <?php endif; ?>
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
