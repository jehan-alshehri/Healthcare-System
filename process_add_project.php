<?php
// Prevent any output before JSON response
ob_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set header to return JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    require_once 'db.php';

    // Check if the request is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input data
        $project_name = sanitize_input($_POST['project_name'] ?? '');
        $plan_start_date = sanitize_input($_POST['plan_start_date'] ?? '');
        $actual_start_date = sanitize_input($_POST['actual_start_date'] ?? '');
        $plan_budget = sanitize_input($_POST['plan_budget'] ?? '');
        $actual_budget = sanitize_input($_POST['actual_budget'] ?? '');
        $project_manager_id = sanitize_input($_POST['project_manager_id'] ?? '');
        $project_description = sanitize_input($_POST['project_description'] ?? '');

        // Validate required fields
        if (empty($project_name)) {
            $response['errors'][] = "Project name is required";
        }

        // If no validation errors, proceed with database insertion
        if (empty($response['errors'])) {
            // Prepare SQL statement
            $sql = "INSERT INTO tbl_project (project_name, plan_start_date, actual_start_date, 
                    plan_budget, actual_budget, project_manager_id, project_description) 
                    VALUES (:project_name, :plan_start_date, :actual_start_date, 
                    :plan_budget, :actual_budget, :project_manager_id, :project_description)";
            
            $stmt = $conn->prepare($sql);
            $plan_start_date = $plan_start_date ?: null;
            $actual_start_date = $actual_start_date ?: null;
            $plan_budget = $plan_budget ?: null;
            $actual_budget = $actual_budget ?: null;
            $project_manager_id = $project_manager_id ?: null;
            
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
                $response['message'] = "Project added successfully!";
            } else {
                $response['errors'][] = "Error adding project to database";
            }
        }
    } else {
        $response['errors'][] = "Invalid request method";
    }
} catch (Exception $e) {
    $response['errors'][] = "Error: " . $e->getMessage();
}

// Clear any output buffers
ob_end_clean();

// Ensure we have a valid response
if (empty($response)) {
    $response = [
        'success' => false,
        'message' => 'No response generated',
        'errors' => ['An unexpected error occurred']
    ];
}

// Return JSON response
echo json_encode($response);
exit;
?> 