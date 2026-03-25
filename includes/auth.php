<?php
/**
 * Authentication Helpers - EduShield
 * Handles registration, login, password hashing
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

/**
 * Register a new user
 * @return array ['success' => bool, 'message' => string]
 */
function registerUser($name, $email, $password, $role = 'student') {
    global $conn;

    $name = trim($name);
    $email = trim($email);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email address.'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Email already registered.'];
    }
    $stmt->close();

    // Hash password and insert
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Registration successful!'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login user
 * @return array ['success' => bool, 'message' => string, 'role' => string|null]
 */
function loginUser($email, $password) {
    global $conn;

    $email = trim($email);

    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.', 'role' => null];
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            setUserSession($user['id'], $user['name'], $user['email'], $user['role']);
            $stmt->close();
            return ['success' => true, 'message' => 'Login successful!', 'role' => $user['role']];
        }
    }

    $stmt->close();
    return ['success' => false, 'message' => 'Invalid email or password.', 'role' => null];
}
