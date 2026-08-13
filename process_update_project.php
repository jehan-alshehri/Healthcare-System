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
        $project_id = sanitize_input($_POST['project_id'] ?? '');
        $project_name = sanitize_input($_POST['project_name'] ?? '');
        $plan_start_date = sanitize_input($_POST['plan_start_date'] ?? '');
        $actual_start_date = sanitize_input($_POST['actual_start_date'] ?? '');
        $plan_budget = sanitize_input($_POST['plan_budget'] ?? '');
        $actual_budget = sanitize_input($_POST['actual_budget'] ?? '');
        $project_manager_id = sanitize_input($_POST['project_manager_id'] ?? '');
        $project_description = sanitize_input($_POST['project_description'] ?? '');

        // Validate required fields
        if (empty($project_id)) {
            throw new Exception('Project ID is required');
        }
        if (empty($project_name)) {
            throw new Exception('Project name is required');
        }

        // Prepare SQL statement
        $sql = "UPDATE tbl_project SET 
                project_name = :project_name,
                plan_start_date = :plan_start_date,
                actual_start_date = :actual_start_date,
                plan_budget = :plan_budget,
                actual_budget = :actual_budget,
                project_manager_id = :project_manager_id,
                project_description = :project_description
                WHERE project_id = :project_id";
        
        $stmt = $conn->prepare($sql);

        $plan_start_date = $plan_start_date ?: null;
        $actual_start_date = $actual_start_date ?: null;
        $plan_budget = $plan_budget ?: null;
        $actual_budget = $actual_budget ?: null;
        $project_manager_id = $project_manager_id ?: null;
        
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':project_name', $project_name);
        $stmt->bindParam(':plan_start_date', $plan_start_date);
        $stmt->bindParam(':actual_start_date', $actual_start_date);
        $stmt->bindParam(':plan_budget', $plan_budget);
        $stmt->bindParam(':actual_budget', $actual_budget);
        $stmt->bindParam(':project_manager_id', $project_manager_id);
        $stmt->bindParam(':project_description', $project_description);
        // Execute the statement
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Project updated successfully!";
        } else {
            throw new Exception('Error updating project');
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