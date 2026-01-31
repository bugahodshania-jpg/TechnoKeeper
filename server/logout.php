<?php
// Logout script for TechnoKeeper

session_start();

// Include database connection
require_once 'db.php';

// Deactivate session in database if session token exists
if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    $stmt = mysqli_prepare($connect, "
        UPDATE user_sessions 
        SET is_active = 0 
        WHERE user_id = ? AND session_token = ?
    ");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $_SESSION['session_token']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Destroy all session data
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page with logout message
header('Location: ../views/login.php?message=logged_out');
exit;
?>
