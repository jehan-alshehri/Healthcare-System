<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Get JSON data from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Check if resource ID is provided
    if (!isset($data['resource_id'])) {
        throw new Exception('Resource ID is required');
    }

    $resource_id = sanitize_input($data['resource_id']);

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Delete the resource
        $sql = "DELETE FROM tbl_resources WHERE resource_id = :resource_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':resource_id', $resource_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Resource deleted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?> 