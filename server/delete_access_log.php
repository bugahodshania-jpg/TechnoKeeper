<?php
header('Content-Type: application/json');

// Include database connection and authentication
require_once 'db.php';
require_once 'auth_check.php';

// Only allow admin users
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.'
    ]);
    exit;
}

// Get current user data
$currentUser = getCurrentUser();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['logId']) || !isset($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$logId = intval($data['logId']);
$password = $data['password'];

// Verify user password
if (!password_verify($password, $currentUser['password_hash'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid password.'
    ]);
    exit;
}

// Delete the access log
$query = "DELETE FROM access_log WHERE log_id = ?";
$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $logId);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Access log deleted successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Access log not found.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete access log.'
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error.'
    ]);
}

mysqli_close($connect);
?>
