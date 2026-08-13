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
        $task_id = sanitize_input($_POST['task_id'] ?? '');
        $task_name = sanitize_input($_POST['task_name'] ?? '');
        $task_plan_start_date = sanitize_input($_POST['task_plan_start_date'] ?? '');
        $task_actual_start_date = sanitize_input($_POST['task_actual_start_date'] ?? '');
        $task_plan_budget = sanitize_input($_POST['task_plan_budget'] ?? '');
        $task_actual_budget = sanitize_input($_POST['task_actual_budget'] ?? '');

        if (empty($task_id)) {
            throw new Exception('Task ID is required');
        }
        if (empty($task_name)) {
            throw new Exception('Task name is required');
        }

        // Begin transaction to ensure data consistency
        $conn->beginTransaction();

        // Update the task data
        $sql = "UPDATE tbl_task SET 
                task_name = :task_name,
                task_plan_start_date = :task_plan_start_date,
                task_actual_start_date = :task_actual_start_date,
                task_plan_budget = :task_plan_budget,
                task_actual_budget = :task_actual_budget
                WHERE task_id = :task_id";
        
        $stmt = $conn->prepare($sql);

        $task_plan_start_date = $task_plan_start_date ?: null;
        $task_actual_start_date = $task_actual_start_date ?: null;
        $task_plan_budget = $task_plan_budget ?: null;
        $task_actual_budget = $task_actual_budget ?: null;

        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':task_name', $task_name);
        $stmt->bindParam(':task_plan_start_date', $task_plan_start_date);
        $stmt->bindParam(':task_actual_start_date', $task_actual_start_date);
        $stmt->bindParam(':task_plan_budget', $task_plan_budget);
        $stmt->bindParam(':task_actual_budget', $task_actual_budget);

        $stmt->execute();

        // Delete old assignments
        $delete_stmt = $conn->prepare("DELETE FROM tbl_task_people WHERE task_id = :task_id");
        $delete_stmt->bindParam(':task_id', $task_id);
        $delete_stmt->execute();

        // Insert new assignments if provided
        if (!empty($_POST['assigned_people'])) {
            foreach ($_POST['assigned_people'] as $person_id) {
                $insert_assignment = $conn->prepare("INSERT INTO tbl_task_people (task_id, person_id) VALUES (:task_id, :person_id)");
                $insert_assignment->bindParam(':task_id', $task_id);
                $insert_assignment->bindParam(':person_id', $person_id);
                $insert_assignment->execute();
            }
        }

        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Task updated successfully!";
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
