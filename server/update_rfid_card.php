<?php
// Include database connection
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get JSON data from request
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    // Validate required fields
    $required_fields = ['card_id', 'rfid_uid', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $card_id = (int)$data['card_id'];
    $rfid_uid = mysqli_real_escape_string($connect, $data['rfid_uid']);
    $status = mysqli_real_escape_string($connect, $data['status']);

    // Validate status values
    $valid_statuses = ['active', 'inactive', 'lost', 'damaged'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Invalid status value');
    }

    // Check if card exists
    $check_query = "SELECT card_id FROM RFID_cards WHERE card_id = $card_id";
    $check_result = mysqli_query($connect, $check_query);
    
    if (mysqli_num_rows($check_result) === 0) {
        throw new Exception('RFID card not found');
    }

    // Check if RFID UID is unique (excluding current card)
    $uid_check_query = "SELECT card_id FROM RFID_cards WHERE rfid_uid = '$rfid_uid' AND card_id != $card_id";
    $uid_check_result = mysqli_query($connect, $uid_check_query);
    
    if (mysqli_num_rows($uid_check_result) > 0) {
        throw new Exception('RFID UID already exists for another card');
    }

    // Update the RFID card - ONLY update rfid_uid and status, preserve user_id
    $update_query = "UPDATE RFID_cards SET 
                    rfid_uid = '$rfid_uid',
                    status = '$status'
                    WHERE card_id = $card_id";

    $result = mysqli_query($connect, $update_query);

    if (!$result) {
        throw new Exception('Database update failed: ' . mysqli_error($connect));
    }

    // Check if any rows were affected
    if (mysqli_affected_rows($connect) === 0) {
        throw new Exception('No changes made to the card');
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'RFID card updated successfully',
        'data' => [
            'card_id' => $card_id,
            'rfid_uid' => $rfid_uid,
            'status' => $status
        ]
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close database connection
mysqli_close($connect);
?>
