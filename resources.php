<?php
require_once 'db.php';

// Fetch all resources with task names
try {
    $sql = "SELECT r.*, t.task_name 
            FROM tbl_resources r 
            LEFT JOIN tbl_task t ON r.task_id = t.task_id 
            ORDER BY r.resource_title";
    $stmt = $conn->query($sql);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all tasks for the dropdown
    $tasks_sql = "SELECT task_id, task_name FROM tbl_task ORDER BY task_name";
    $tasks_stmt = $conn->query($tasks_sql);
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
    <title>Resources - Healthcare Project Management System</title>
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
                        <a class="nav-link" href="view_tasks.php">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="resources.php">Resources</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Resources</h1>
            <button type="button" class="btn btn-add" onclick="showAddResourceModal()">
                <i class="fas fa-plus me-2"></i>Add New Resource
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
                            <th>Resource Name</th>
                            <th>Resource Type</th>
                            <th>Linked Task</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resources)): ?>
                            <?php foreach ($resources as $resource): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resource['resource_title']); ?></td>
                                    <td><?php echo htmlspecialchars($resource['resource_type']); ?></td>
                            
                                    <td><?php echo htmlspecialchars($resource['task_name'] ?? 'Not Assigned'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-action" title="Edit"
                                                onclick='showEditResourceModal(<?php echo json_encode($resource); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-action" title="Delete"
                                                onclick="confirmDeleteResource(<?php echo $resource['resource_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No resources found</td>
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
        // Function to show the Add Resource modal
        function showAddResourceModal() {
            Swal.fire({
                title: 'Add New Resource',
                html: `
                    <form id="addResourceForm" class="text-start">
                        <div class="mb-3">
                            <label class="form-label">Resource Name</label>
                            <input type="text" class="form-control" name="resource_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resource Type</label>
                            <select class="form-select" name="resource_type" required>
                                <option value="">Select Type</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Software">Software</option>
                                <option value="Service">Service</option>
                                <option value="Material">Material</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Linked Task</label>
                            <select class="form-select" name="task_id">
                                <option value="">Select Task</option>
                                <?php foreach ($tasks as $task): ?>
                                    <option value="<?php echo $task['task_id']; ?>">
                                        <?php echo htmlspecialchars($task['task_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Resource',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#666666',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('addResourceForm');
                    const formData = new FormData(form);
                    return fetch('process_add_resource.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error adding resource');
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
                        text: 'Resource added successfully',
                        icon: 'success',
                        confirmButtonColor: '#2ecc71'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Function to show the Edit Resource modal
        function showEditResourceModal(resource) {
            Swal.fire({
                title: 'Edit Resource',
                html: `
                    <form id="editResourceForm" class="text-start">
                        <input type="hidden" name="resource_id" value="${resource.resource_id}">
                        <div class="mb-3">
                            <label class="form-label">Resource Name</label>
                            <input type="text" class="form-control" name="resource_title" value="${resource.resource_title || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resource Type</label>
                            <select class="form-select" name="resource_type" required>
                                <option value="">Select Type</option>
                                <option value="Equipment" ${resource.resource_type === 'Equipment' ? 'selected' : ''}>Equipment</option>
                                <option value="Software" ${resource.resource_type === 'Software' ? 'selected' : ''}>Software</option>
                                <option value="Service" ${resource.resource_type === 'Service' ? 'selected' : ''}>Service</option>
                                <option value="Material" ${resource.resource_type === 'Material' ? 'selected' : ''}>Material</option>
                                <option value="Other" ${resource.resource_type === 'Other' ? 'selected' : ''}>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Linked Task</label>
                            <select class="form-select" name="task_id">
                                <option value="">Select Task</option>
                                <?php foreach ($tasks as $task): ?>
                                    <option value="<?php echo $task['task_id']; ?>" ${resource.task_id == <?php echo $task['task_id']; ?> ? 'selected' : ''}>
                                        <?php echo htmlspecialchars($task['task_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update Resource',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#666666',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('editResourceForm');
                    const formData = new FormData(form);
                    return fetch('process_update_resource.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error updating resource');
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
                        text: 'Resource updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#2ecc71'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Function to confirm resource deletion
        function confirmDeleteResource(resourceId) {
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
                    fetch('process_delete_resource.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ resource_id: resourceId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Resource has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#2ecc71'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Error deleting resource',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error deleting resource',
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