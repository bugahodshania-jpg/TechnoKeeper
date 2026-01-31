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

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['logId']) || !isset($data['status']) || !isset($data['method'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$logId = intval($data['logId']);
$status = $data['status'];
$method = $data['method'];

// Validate status and method
$validStatuses = ['granted', 'denied'];
$validMethods = ['RFID', 'PIN', 'OVERRIDE'];

if (!in_array($status, $validStatuses) || !in_array($method, $validMethods)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status or method value.'
    ]);
    exit;
}

if ($logId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid log ID.'
    ]);
    exit;
}

// Update the access log
$query = "UPDATE access_log SET access_status = ?, method_access = ? WHERE log_id = ?";
$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ssi', $status, $method, $logId);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Access log updated successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Access log not found or no changes made.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update access log.'
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
