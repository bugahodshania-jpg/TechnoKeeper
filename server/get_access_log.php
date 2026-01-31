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

// Get log ID
if (!isset($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Log ID is required.'
    ]);
    exit;
}

$logId = intval($_GET['id']);

if ($logId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid log ID.'
    ]);
    exit;
}

// Fetch the access log
$query = "SELECT 
            al.log_id,
            al.access_status,
            al.method_access,
            al.access_timestamp,
            CASE 
                WHEN u.first_name IS NOT NULL AND u.last_name IS NOT NULL 
                THEN CONCAT(u.first_name, ' ', u.last_name)
                WHEN ai.first_name IS NOT NULL AND ai.last_name IS NOT NULL 
                THEN CONCAT(ai.first_name, ' ', ai.last_name)
                ELSE 'Unknown'
            END as name,
            rc.rfid_uid as card_rfid,
            DATE(al.access_timestamp) as date,
            TIME(al.access_timestamp) as time
          FROM access_log al
          LEFT JOIN users u ON al.user_id = u.id
          LEFT JOIN admin_information ai ON al.admin_info_id = ai.admin_info_id
          LEFT JOIN RFID_cards rc ON al.card_id = rc.card_id
          WHERE al.log_id = ?";

$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $logId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'log' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Access log not found.'
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
