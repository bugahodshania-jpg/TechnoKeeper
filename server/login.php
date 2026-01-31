<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once 'db.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to send JSON response
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Get and sanitize input data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($password)) {
    sendResponse(false, 'Username and password are required');
}

// Prepare statement to prevent SQL injection
$stmt = mysqli_prepare($connect, "
    SELECT id, user_id, first_name, last_name, username, email, password_hash, role, is_active 
    FROM users 
    WHERE username = ? OR email = ?
    LIMIT 1
");

if (!$stmt) {
    sendResponse(false, 'Database error: ' . mysqli_error($connect));
}

// Bind parameters
mysqli_stmt_bind_param($stmt, "ss", $username, $username);

// Execute statement
mysqli_stmt_execute($stmt);

// Get result
$result = mysqli_stmt_get_result($stmt);

if ($result === false) {
    sendResponse(false, 'Database error: ' . mysqli_error($connect));
}

// Check if user exists
if (mysqli_num_rows($result) === 0) {
    sendResponse(false, 'Invalid username or password');
}

// Fetch user data
$user = mysqli_fetch_assoc($result);

// Check if user account is active
if (!$user['is_active']) {
    sendResponse(false, 'Your account has been deactivated. Please contact administrator.');
}

// Verify password
if (!password_verify($password, $user['password_hash'])) {
    sendResponse(false, 'Invalid username or password');
}

// Check for session lockout (optional security feature)
$lockCheck = mysqli_prepare($connect, "
    SELECT failed_attempts, locked_until 
    FROM user_sessions 
    WHERE user_id = ? AND is_active = 1 
    ORDER BY created_at DESC 
    LIMIT 1
");

if ($lockCheck) {
    mysqli_stmt_bind_param($lockCheck, "i", $user['id']);
    mysqli_stmt_execute($lockCheck);
    $lockResult = mysqli_stmt_get_result($lockCheck);
    
    if ($lockResult && mysqli_num_rows($lockResult) > 0) {
        $sessionData = mysqli_fetch_assoc($lockResult);
        
        // Check if account is locked
        if ($sessionData['locked_until'] && strtotime($sessionData['locked_until']) > time()) {
            $lockTime = strtotime($sessionData['locked_until']) - time();
            $minutes = ceil($lockTime / 60);
            sendResponse(false, "Account temporarily locked. Try again in {$minutes} minutes.");
        }
        
        // Reset failed attempts on successful login
        if ($sessionData['failed_attempts'] > 0) {
            $resetStmt = mysqli_prepare($connect, "
                UPDATE user_sessions 
                SET failed_attempts = 0, locked_until = NULL 
                WHERE user_id = ? AND is_active = 1
            ");
            if ($resetStmt) {
                mysqli_stmt_bind_param($resetStmt, "i", $user['id']);
                mysqli_stmt_execute($resetStmt);
            }
        }
    }
}

// Generate session token
$sessionToken = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Store session in database
$sessionStmt = mysqli_prepare($connect, "
    INSERT INTO user_sessions (user_id, session_token, expires_at, is_active) 
    VALUES (?, ?, ?, 1)
");

if (!$sessionStmt) {
    sendResponse(false, 'Session creation failed: ' . mysqli_error($connect));
}

mysqli_stmt_bind_param($sessionStmt, "iss", $user['id'], $sessionToken, $expiresAt);

if (!mysqli_stmt_execute($sessionStmt)) {
    sendResponse(false, 'Failed to create session: ' . mysqli_error($connect));
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_uid'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['session_token'] = $sessionToken;
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

// Determine redirect page based on role
$redirectPage = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php';

// Send success response
sendResponse(true, 'Login successful', [
    'redirect' => $redirectPage,
    'user' => [
        'id' => $user['id'],
        'user_id' => $user['user_id'],
        'username' => $user['username'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'role' => $user['role']
    ]
]);

// Close statements and connection
mysqli_stmt_close($stmt);
mysqli_stmt_close($sessionStmt);
if (isset($lockCheck)) mysqli_stmt_close($lockCheck);
mysqli_close($connect);
?>
