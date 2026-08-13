<?php
require_once 'db.php';

// Fetch all projects with manager names
try {
    $sql = "SELECT p.*, m.name as manager_name 
            FROM tbl_project p 
            LEFT JOIN tbl_people m ON p.project_manager_id = m.id 
            ORDER BY p.project_name";
    $stmt = $conn->query($sql);
    $projects = $stmt->fetchAll();

    $people_sql = "SELECT id, name FROM tbl_people ORDER BY name";
    $people_stmt = $conn->query($people_sql);
    $people = $people_stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Healthcare Project Management System</title>
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
            --light-bg:#f3f0d3;
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

        .description-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                        <a class="nav-link active" href="projects.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_tasks.php">Tasks</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Projects</h1>
            <button type="button" class="btn btn-add" onclick="showAddProjectModal()">
                <i class="fas fa-plus me-2"></i>Add New Project
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
                            <th>Project Name</th>
                            <th>Planned Start</th>
                            <th>Actual Start</th>
                            <th>Planned Budget</th>
                            <th>Actual Budget</th>
                            <th>Project Manager</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($projects) && !empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                                    <td><?php echo $project['plan_start_date'] ? date('Y-m-d', strtotime($project['plan_start_date'])) : '-'; ?></td>
                                    <td><?php echo $project['actual_start_date'] ? date('Y-m-d', strtotime($project['actual_start_date'])) : '-'; ?></td>
                                    <td><?php echo $project['plan_budget'] ? number_format($project['plan_budget'], 2) : '-'; ?></td>
                                    <td><?php echo $project['actual_budget'] ? number_format($project['actual_budget'], 2) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($project['manager_name'] ?? 'Not Assigned'); ?></td>
                                    <td class="description-cell" title="<?php echo htmlspecialchars($project['project_description']); ?>">
                                        <?php echo htmlspecialchars($project['project_description']); ?>
                                    </td>
                                    <td>
                                      <button type="button" class="btn btn-sm btn-primary btn-action" title="Edit"
    onclick='showEditProjectModal(<?php echo json_encode($project); ?>)'>
    <i class="fas fa-edit"></i>
</button>

                                        <a href="view_tasks.php?project_id=<?php echo $project['project_id']; ?>" class="btn btn-sm btn-info btn-action" title="View Tasks">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger btn-action" title="Delete" 
                                                onclick="confirmDelete(<?php echo $project['project_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No projects found</td>
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
        function showAddProjectModal() {
            Swal.fire({
                title: 'Add New Project',
                html: `
                    <form id="addProjectForm" class="text-start">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project_name" name="project_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="plan_start_date" class="form-label">Planned Start Date</label>
                            <input type="date" class="form-control" id="plan_start_date" name="plan_start_date">
                        </div>
                        <div class="mb-3">
                            <label for="actual_start_date" class="form-label">Actual Start Date</label>
                            <input type="date" class="form-control" id="actual_start_date" name="actual_start_date">
                        </div>
                        <div class="mb-3">
                            <label for="plan_budget" class="form-label">Planned Budget</label>
                            <input type="number" class="form-control" id="plan_budget" name="plan_budget" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="actual_budget" class="form-label">Actual Budget</label>
                            <input type="number" class="form-control" id="actual_budget" name="actual_budget" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="project_manager_id" class="form-label">Project Manager</label>
                            <select class="form-select" id="project_manager_id" name="project_manager_id">
                                <option value="">Select Manager</option>
                                <?php foreach ($people as $person): ?>
                                    <option value="<?php echo $person['id']; ?>"><?php echo htmlspecialchars($person['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="project_description" class="form-label">Project Description</label>
                            <textarea class="form-control" id="project_description" name="project_description" rows="3"></textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Project',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#666666',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('addProjectForm');
                    const formData = new FormData(form);
                    return fetch('process_add_project.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error adding project');
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
                        text: 'Project added successfully',
                        icon: 'success',
                        confirmButtonColor: '#2ecc71'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        function showEditProjectModal(project) {
    Swal.fire({
        title: 'Edit Project',
        html: `
            <form id="editProjectForm" class="text-start">
                <input type="hidden" name="project_id" value="${project.project_id}">
                <div class="mb-3">
                    <label class="form-label">Project Name</label>
                    <input type="text" class="form-control" name="project_name" value="${project.project_name || ''}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Planned Start Date</label>
                    <input type="date" class="form-control" name="plan_start_date" value="${project.plan_start_date || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Actual Start Date</label>
                    <input type="date" class="form-control" name="actual_start_date" value="${project.actual_start_date || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Planned Budget</label>
                    <input type="number" class="form-control" name="plan_budget" value="${project.plan_budget || ''}" step="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Actual Budget</label>
                    <input type="number" class="form-control" name="actual_budget" value="${project.actual_budget || ''}" step="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Project Manager</label>
                    <select class="form-select" name="project_manager_id">
                        <option value="">Select Manager</option>
                        <?php foreach ($people as $person): ?>
                            <option value="<?php echo $person['id']; ?>" id="manager_<?php echo $person['id']; ?>">
                                <?php echo htmlspecialchars($person['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Project Description</label>
                    <textarea class="form-control" name="project_description" rows="3">${project.project_description || ''}</textarea>
                </div>
            </form>
        `,
        didOpen: () => {
            // بعد فتح السويت أليرت، نحدد المدير المختار
            if (project.project_manager_id) {
                const select = Swal.getPopup().querySelector('select[name="project_manager_id"]');
                select.value = project.project_manager_id;
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Update Project',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2ecc71',
        cancelButtonColor: '#666666',
        focusConfirm: false,
        preConfirm: () => {
            const form = document.getElementById('editProjectForm');
            const formData = new FormData(form);
            return fetch('process_update_project.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Error updating project');
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
                text: 'Project updated successfully',
                icon: 'success',
                confirmButtonColor: '#2ecc71'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

        function confirmDelete(projectId) {
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
                    fetch('process_delete_project.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ project_id: projectId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Project has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#2ecc71'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Error deleting project',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error deleting project',
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