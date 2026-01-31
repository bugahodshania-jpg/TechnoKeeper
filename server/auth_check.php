<?php
// Authentication check script
// Include this at the top of any protected page

session_start();

// Include database connection
require_once 'db.php';

// Function to check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to validate session token in database
function validateSession($userId, $sessionToken) {
    global $connect;
    
    $stmt = mysqli_prepare($connect, "
        SELECT id, expires_at, is_active 
        FROM user_sessions 
        WHERE user_id = ? AND session_token = ? AND is_active = 1
        LIMIT 1
    ");
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "is", $userId, $sessionToken);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $session = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Check if session has expired
    if (strtotime($session['expires_at']) < time()) {
        // Deactivate expired session
        $deactivateStmt = mysqli_prepare($connect, "
            UPDATE user_sessions 
            SET is_active = 0 
            WHERE id = ?
        ");
        if ($deactivateStmt) {
            mysqli_stmt_bind_param($deactivateStmt, "i", $session['id']);
            mysqli_stmt_execute($deactivateStmt);
            mysqli_stmt_close($deactivateStmt);
        }
        return false;
    }
    
    return true;
}

// Main authentication check
if (!isAuthenticated()) {
    // User not logged in, redirect to login page
    header('Location: ../views/login.php');
    exit;
}

// Validate session token if available
if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    if (!validateSession($_SESSION['user_id'], $_SESSION['session_token'])) {
        // Session invalid, destroy session and redirect
        session_destroy();
        header('Location: ../views/login.php?error=session_expired');
        exit;
    }
}

// Function to get current user data
function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'user_id' => $_SESSION['user_uid'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'first_name' => $_SESSION['first_name'] ?? null,
        'last_name' => $_SESSION['last_name'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

// Function to check if current user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to logout user
function logoutUser() {
    global $connect;
    
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
    
    // Destroy session
    session_destroy();
    
    // Redirect to login page
    header('Location: ../views/login.php');
    exit;
}
?>
