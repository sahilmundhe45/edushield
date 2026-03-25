<?php
/**
 * Session Management - EduShield
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user's ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's role
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user's name
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Require login – redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /edushield/login.php?error=Please+login+first");
        exit();
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (getCurrentUserRole() !== 'admin') {
        header("Location: /edushield/index.php?error=Unauthorized+access");
        exit();
    }
}

/**
 * Require student role
 */
function requireStudent() {
    requireLogin();
    if (getCurrentUserRole() !== 'student') {
        header("Location: /edushield/index.php?error=Unauthorized+access");
        exit();
    }
}

/**
 * Set user session after login
 */
function setUserSession($id, $name, $email, $role) {
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
}

/**
 * Destroy session on logout
 */
function destroySession() {
    session_unset();
    session_destroy();
}
