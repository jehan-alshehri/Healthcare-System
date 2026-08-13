<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $project_id = sanitize_input($_POST['project_id'] ?? '');
        $task_name = sanitize_input($_POST['task_name'] ?? '');
        $task_plan_start_date = sanitize_input($_POST['task_plan_start_date'] ?? '');
        $task_actual_start_date = sanitize_input($_POST['task_actual_start_date'] ?? '');
        $task_plan_budget = sanitize_input($_POST['task_plan_budget'] ?? '');
        $task_actual_budget = sanitize_input($_POST['task_actual_budget'] ?? '');

        if (empty($project_id)) {
            throw new Exception('Project ID is required');
        }
        if (empty($task_name)) {
            throw new Exception('Task name is required');
        }

        $sql = "INSERT INTO tbl_task (project_id, task_name, task_plan_start_date, 
                task_actual_start_date, task_plan_budget, task_actual_budget) 
                VALUES (:project_id, :task_name, :task_plan_start_date, 
                :task_actual_start_date, :task_plan_budget, :task_actual_budget)";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':task_name', $task_name);
        $task_plan_start_date = $task_plan_start_date ?: null;
        $task_actual_start_date = $task_actual_start_date ?: null;
        $task_plan_budget = $task_plan_budget ?: null;
        $task_actual_budget = $task_actual_budget ?: null;
        $stmt->bindParam(':task_plan_start_date', $task_plan_start_date);
        $stmt->bindParam(':task_actual_start_date', $task_actual_start_date);
        $stmt->bindParam(':task_plan_budget', $task_plan_budget);
        $stmt->bindParam(':task_actual_budget', $task_actual_budget);

        if ($stmt->execute()) {
            // احفظ الأشخاص المرتبطين
            $task_id = $conn->lastInsertId();

            if (!empty($_POST['assigned_people'])) {
                foreach ($_POST['assigned_people'] as $person_id) {
                    $insert_assignment = $conn->prepare("INSERT INTO tbl_task_people (task_id, person_id) VALUES (:task_id, :person_id)");
                    $insert_assignment->bindParam(':task_id', $task_id);
                    $insert_assignment->bindParam(':person_id', $person_id);
                    $insert_assignment->execute();
                }
            }

            $response['success'] = true;
            $response['message'] = "Task added successfully!";
        } else {
            throw new Exception('Error adding task');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
