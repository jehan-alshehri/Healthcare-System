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

    // Check if project ID is provided
    if (!isset($data['project_id'])) {
        throw new Exception('Project ID is required');
    }

    $project_id = sanitize_input($data['project_id']);

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Delete related tasks first (due to foreign key constraint)
        $sql = "DELETE FROM tbl_task WHERE project_id = :project_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();

        // Delete the project
        $sql = "DELETE FROM tbl_project WHERE project_id = :project_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Project deleted successfully!";
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