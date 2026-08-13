<?php
require_once 'db.php';

// Set header to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'task' => null,
    'assigned_people' => []
];

try {
    $task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (empty($task_id)) {
        throw new Exception('Task ID is required');
    }

    // Get task details
    $task_stmt = $conn->prepare("SELECT * FROM tbl_task WHERE task_id = :task_id");
    $task_stmt->bindParam(':task_id', $task_id);
    $task_stmt->execute();
    $task = $task_stmt->fetch();

    if (!$task) {
        throw new Exception('Task not found');
    }

    // Get assigned people
    $people_stmt = $conn->prepare("SELECT person_id FROM tbl_task_people WHERE task_id = :task_id");
    $people_stmt->bindParam(':task_id', $task_id);
    $people_stmt->execute();
    $assigned_people = $people_stmt->fetchAll(PDO::FETCH_COLUMN);

    $response['success'] = true;
    $response['task'] = $task;
    $response['assigned_people'] = $assigned_people;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
