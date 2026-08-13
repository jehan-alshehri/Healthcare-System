<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'project' => null
];

try {
    // Check if project ID is provided
    if (!isset($_GET['id'])) {
        throw new Exception('Project ID is required');
    }

    $project_id = sanitize_input($_GET['id']);

    // Fetch project data
    $sql = "SELECT * FROM tbl_project WHERE project_id = :project_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();

    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        $response['success'] = true;
        $response['project'] = $project;
    } else {
        throw new Exception('Project not found');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?> 