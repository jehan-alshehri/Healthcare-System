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

    // Check if task ID is provided
    if (!isset($data['task_id'])) {
        throw new Exception('Task ID is required');
    }

    $task_id = sanitize_input($data['task_id']);

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Delete related resources first (due to foreign key constraint)
        $sql = "DELETE FROM tbl_resources WHERE task_id = :task_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();

        // Delete the task
        $sql = "DELETE FROM tbl_task WHERE task_id = :task_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Task deleted successfully!";
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