<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    // Check if the request is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input data
        $resource_id = sanitize_input($_POST['resource_id'] ?? '');
        $resource_title = sanitize_input($_POST['resource_title'] ?? '');
        $resource_type = sanitize_input($_POST['resource_type'] ?? '');
        $task_id = sanitize_input($_POST['task_id'] ?? '');

        // Validate required fields
        if (empty($resource_id)) {
            throw new Exception('Resource ID is required');
        }
        if (empty($resource_title)) {
            throw new Exception('Resource name is required');
        }
        if (empty($resource_type)) {
            throw new Exception('Resource type is required');
        }
       
        $task_id = $task_id !== '' ? $task_id : null;
        // Prepare SQL statement
        $sql = "UPDATE tbl_resources SET 
                resource_title = :resource_title,
                resource_type = :resource_type,
                task_id = :task_id
                WHERE resource_id = :resource_id";
        
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':resource_id', $resource_id);
        $stmt->bindParam(':resource_title', $resource_title);
        $stmt->bindParam(':resource_type', $resource_type);
        $stmt->bindParam(':task_id', $task_id);

        // Execute the statement
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Resource updated successfully!";
        } else {
            throw new Exception('Error updating resource');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?> 