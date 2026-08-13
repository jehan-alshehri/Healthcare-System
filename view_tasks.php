<?php
require_once 'db.php';

// Get project ID from URL
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Fetch project details
try {
    $project_sql = "SELECT p.*, m.name as manager_name 
                    FROM tbl_project p 
                    LEFT JOIN tbl_people m ON p.project_manager_id = m.id 
                    WHERE p.project_id = :project_id";
    $project_stmt = $conn->prepare($project_sql);
    $project_stmt->bindParam(':project_id', $project_id);
    $project_stmt->execute();
    $project = $project_stmt->fetch(PDO::FETCH_ASSOC);
    
    $people_stmt = $conn->query("SELECT id, name FROM tbl_people ORDER BY name");
    $people = $people_stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$project) {
        header('Location: projects.php');
        exit;
    }

    // Fetch tasks for this project
    $tasks_sql = "SELECT t.*, GROUP_CONCAT(p.name SEPARATOR ', ') as assigned_people 
    FROM tbl_task t
    LEFT JOIN tbl_task_people tp ON t.task_id = tp.task_id
    LEFT JOIN tbl_people p ON tp.person_id = p.id
    WHERE t.project_id = :project_id
    GROUP BY t.task_id
    ORDER BY t.task_name";
    $tasks_stmt = $conn->prepare($tasks_sql);
    $tasks_stmt->bindParam(':project_id', $project_id);
    $tasks_stmt->execute();
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Tasks - Healthcare Project Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-bg: #FFFDE8;
            --card-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #666666;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
        }

        .page-title {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
            border-radius: 2px;
        }

        .table-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 0.25rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-add {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
            transform: translateY(-2px);
        }

        .project-info {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .project-info h2 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .project-info p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            border-radius: 15px;
        }

        .swal2-title {
            color: var(--text-primary);
        }

        .swal2-html-container {
            color: var(--text-secondary);
        }

        .swal2-confirm {
            background-color: var(--accent-color) !important;
        }

        .swal2-cancel {
            background-color: var(--text-secondary) !important;
        }

        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 8px;
            }
            
            .btn-action {
                padding: 0.2rem 0.4rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Healthcare PM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="projects.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="view_tasks.php">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="resources.php">Resources</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_people.php">People</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Project Info -->
        <div class="project-info">
            <h2><?php echo htmlspecialchars($project['project_name']); ?></h2>
            <p><strong>Project Manager:</strong> <?php echo htmlspecialchars($project['manager_name'] ?? 'Not Assigned'); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($project['project_description']); ?></p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Project Tasks</h1>
            <button type="button" class="btn btn-add" onclick="showAddTaskModal()">
                <i class="fas fa-plus me-2"></i>Add New Task
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Assigned To</th>
                            <th>Planned Start</th>
                            <th>Actual Start</th>
                            <th>Planned Budget</th>
                            <th>Actual Budget</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tasks)): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                    <td><?php echo htmlspecialchars($task['assigned_people']) ?: '-'; ?></td>
                                    <td><?php echo $task['task_plan_start_date'] ? date('Y-m-d', strtotime($task['task_plan_start_date'])) : '-'; ?></td>
                                    <td><?php echo $task['task_actual_start_date'] ? date('Y-m-d', strtotime($task['task_actual_start_date'])) : '-'; ?></td>
                                    <td><?php echo $task['task_plan_budget'] ? number_format($task['task_plan_budget'], 2) : '-'; ?></td>
                                    <td><?php echo $task['task_actual_budget'] ? number_format($task['task_actual_budget'], 2) : '-'; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-action" title="Edit"
                                                onclick='showEditTaskModal(<?php echo json_encode($task); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-action" title="Delete"
                                                onclick="confirmDeleteTask(<?php echo $task['task_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No tasks found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Function to show the Add Task modal
        function showAddTaskModal() {
            Swal.fire({
                title: 'Add New Task',
                html: `
                    <form id="addTaskForm" class="text-start">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            <input type="text" class="form-control" name="task_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Planned Start Date</label>
                            <input type="date" class="form-control" name="task_plan_start_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Actual Start Date</label>
                            <input type="date" class="form-control" name="task_actual_start_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Planned Budget</label>
                            <input type="number" class="form-control" name="task_plan_budget" step="0.01">
                        </div>
                        <div class="mb-3">
    <label class="form-label">Assigned To</label>
    <select class="form-select" name="assigned_people[]" multiple>
        <?php foreach ($people as $person): ?>
            <option value="<?php echo $person['id']; ?>"><?php echo htmlspecialchars($person['name']); ?></option>
        <?php endforeach; ?>
    </select>
</div>
                        <div class="mb-3">
                            <label class="form-label">Actual Budget</label>
                            <input type="number" class="form-control" name="task_actual_budget" step="0.01">
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Task',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#666666',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('addTaskForm');
                    const formData = new FormData(form);
                    return fetch('process_add_task.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error adding task');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Task added successfully',
                        icon: 'success',
                        confirmButtonColor: '#2ecc71'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Function to show the Edit Task modal
        function showEditTaskModal(task) {
    fetch(`get_task.php?id=${task.task_id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const task = data.task;
                const assignedPeople = data.assigned_people;

                let peopleOptions = `
                    <?php foreach ($people as $person): ?>
                        <option value="<?php echo $person['id']; ?>" 
                            ${assignedPeople.includes(<?php echo $person['id']; ?>) ? 'selected' : ''}>
                            <?php echo htmlspecialchars($person['name']); ?>
                        </option>
                    <?php endforeach; ?>
                `;

                Swal.fire({
                    title: 'Edit Task',
                    html: `
                        <form id="editTaskForm" class="text-start">
                            <input type="hidden" name="task_id" value="${task.task_id}">
                            <div class="mb-3">
                                <label class="form-label">Task Name</label>
                                <input type="text" class="form-control" name="task_name" value="${task.task_name || ''}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Planned Start Date</label>
                                <input type="date" class="form-control" name="task_plan_start_date" value="${task.task_plan_start_date || ''}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Actual Start Date</label>
                                <input type="date" class="form-control" name="task_actual_start_date" value="${task.task_actual_start_date || ''}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Planned Budget</label>
                                <input type="number" class="form-control" name="task_plan_budget" value="${task.task_plan_budget || ''}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Actual Budget</label>
                                <input type="number" class="form-control" name="task_actual_budget" value="${task.task_actual_budget || ''}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assigned To</label>
                                <select class="form-select" name="assigned_people[]" multiple>
                                    ${peopleOptions}
                                </select>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update Task',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#2ecc71',
                    cancelButtonColor: '#666666',
                    focusConfirm: false,
                    preConfirm: () => {
                        const form = document.getElementById('editTaskForm');
                        const formData = new FormData(form);
                        return fetch('process_update_task.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'Error updating task');
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Task updated successfully',
                            icon: 'success',
                            confirmButtonColor: '#2ecc71'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });

            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to fetch task data',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'Error fetching task details',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
}


        // Function to confirm task deletion
        function confirmDeleteTask(taskId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#666666',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process_delete_task.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ task_id: taskId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Task has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#2ecc71'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Error deleting task',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error deleting task',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html> 