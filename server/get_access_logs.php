<?php
header('Content-Type: application/json');

// Include database connection
require_once 'db.php';

// Get limit parameter
$limit = isset($_GET['limit']) && $_GET['limit'] === 'all' ? '' : 'LIMIT 20';

try {
    // Query to get access logs
    $query = "SELECT 
                al.log_id,
                CASE 
                    WHEN u.first_name IS NOT NULL AND u.last_name IS NOT NULL 
                    THEN CONCAT(u.first_name, ' ', u.last_name)
                    WHEN ai.first_name IS NOT NULL AND ai.last_name IS NOT NULL 
                    THEN CONCAT(ai.first_name, ' ', ai.last_name)
                    ELSE 'Unknown'
                END as name,
                rc.rfid_uid as card_rfid,
                DATE(al.access_timestamp) as date,
                TIME(al.access_timestamp) as time,
                al.access_status
              FROM access_log al
              LEFT JOIN users u ON al.user_id = u.id
              LEFT JOIN admin_information ai ON al.admin_info_id = ai.admin_info_id
              LEFT JOIN RFID_cards rc ON al.card_id = rc.card_id
              ORDER BY al.access_timestamp DESC $limit";
    
    $result = mysqli_query($connect, $query);
    
    $logs = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'logs' => $logs
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($connect);
?>
