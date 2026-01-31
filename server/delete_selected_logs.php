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

if (!isset($data['logIds']) || !isset($data['password']) || !is_array($data['logIds'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$logIds = $data['logIds'];
$password = $data['password'];

// Verify user password
if (!password_verify($password, $currentUser['password_hash'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid password.'
    ]);
    exit;
}

// Convert log IDs to integers and validate
$logIds = array_map('intval', $logIds);
$logIds = array_filter($logIds, function($id) { return $id > 0; });

if (empty($logIds)) {
    echo json_encode([
        'success' => false,
        'message' => 'No valid log IDs provided.'
    ]);
    exit;
}

// Create placeholders for IN clause
$placeholders = str_repeat('?,', count($logIds) - 1) . '?';
$types = str_repeat('i', count($logIds));

// Delete the selected access logs
$query = "DELETE FROM access_log WHERE log_id IN ($placeholders)";
$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$logIds);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        echo json_encode([
            'success' => true,
            'message' => "$affectedRows access logs deleted successfully.",
            'deleted_count' => $affectedRows
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete access logs.'
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
