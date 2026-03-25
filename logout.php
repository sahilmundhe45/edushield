<?php
/**
 * EduShield – Logout
 */
require_once 'includes/session.php';
destroySession();
header("Location: /edushield/login.php?success=" . urlencode("You have been logged out successfully."));
exit();
