<?php
require_once 'db.php';

// Initialize variables for statistics
$stats = [
    'total_projects' => 0,
    'total_tasks' => 0,
    'total_team_members' => 0,
    'total_resources' => 0
];

// Initialize arrays for charts
$resource_types = [];
$project_budgets = [];

try {
    // Get total projects count
    $sql = "SELECT COUNT(*) as count FROM tbl_project";
    $stmt = $conn->query($sql);
    $stats['total_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get total tasks count
    $sql = "SELECT COUNT(*) as count FROM tbl_task";
    $stmt = $conn->query($sql);
    $stats['total_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get total team members count
    $sql = "SELECT COUNT(*) as count FROM tbl_people";
    $stmt = $conn->query($sql);
    $stats['total_team_members'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get total resources count
    $sql = "SELECT COUNT(*) as count FROM tbl_resources";
    $stmt = $conn->query($sql);
    $stats['total_resources'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get resource types distribution
    $sql = "SELECT resource_type, COUNT(*) as count FROM tbl_resources GROUP BY resource_type";
    $stmt = $conn->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resource_types[] = [
            'type' => $row['resource_type'],
            'count' => $row['count']
        ];
    }

    // Get project budgets
    $sql = "SELECT project_name, plan_budget, actual_budget FROM tbl_project ORDER BY project_name";
    $stmt = $conn->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_budgets[] = [
            'name' => $row['project_name'],
            'planned' => $row['plan_budget'],
            'actual' => $row['actual_budget']
        ];
    }

    // Get detailed project list
    $sql = "SELECT p.*, 
            COUNT(DISTINCT t.task_id) as total_tasks,
            COUNT(DISTINCT r.resource_id) as total_resources,
            pe.name as project_manager_name
            FROM tbl_project p
            LEFT JOIN tbl_task t ON p.project_id = t.project_id
            LEFT JOIN tbl_resources r ON t.task_id = r.task_id
            LEFT JOIN tbl_people pe ON p.project_manager_id = pe.id
            GROUP BY p.project_id
            ORDER BY p.project_name";
    $stmt = $conn->query($sql);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Healthcare Project Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .stats-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .chart-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
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

        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .chart-container {
                margin-bottom: 1rem;
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
                        <a class="nav-link" href="#">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="resources.php">Resources</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_people.php">People</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <h1 class="page-title text-center">Project Reports</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total_projects']; ?></div>
                    <div class="stats-label">Total Projects</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total_tasks']; ?></div>
                    <div class="stats-label">Total Tasks</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total_team_members']; ?></div>
                    <div class="stats-label">Team Members</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total_resources']; ?></div>
                    <div class="stats-label">Resources</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="chart-container" style="max-width: 400px; margin: 0 auto;">
                    <h4 class="mb-4">Resource Type Distribution</h4>
                    <canvas id="resourceTypeChart"></canvas>
                </div>
            </div>
            <div class="col-md-8">
                <div class="chart-container">
                    <h4 class="mb-4">Project Budget Comparison</h4>
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="table-container">
            <h4 class="mb-4">Project Details</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Project Manager</th>
                            <th>Plan Start Date</th>
                            <th>Actual Start Date</th>
                            <th>Plan Budget</th>
                            <th>Actual Budget</th>
                            <th>Tasks</th>
                            <th>Resources</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                                    <td><?php echo htmlspecialchars($project['project_manager_name'] ?? 'Not Assigned'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($project['plan_start_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($project['actual_start_date'])); ?></td>
                                    <td><?php echo number_format($project['plan_budget'], 2); ?>SR</td>
                                    <td><?php echo number_format($project['actual_budget'], 2); ?>SR</td>
                                    <td><?php echo $project['total_tasks']; ?></td>
                                    <td><?php echo $project['total_resources']; ?></td>
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
        // Resource Type Distribution Chart
        const resourceTypeData = <?php echo json_encode($resource_types); ?>;
        new Chart(document.getElementById('resourceTypeChart'), {
            type: 'pie',
            data: {
                labels: resourceTypeData.map(item => item.type),
                datasets: [{
                    data: resourceTypeData.map(item => item.count),
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#e74c3c',
                        '#f1c40f',
                        '#9b59b6'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Project Budget Comparison Chart
        const budgetData = <?php echo json_encode($project_budgets); ?>;
        new Chart(document.getElementById('budgetChart'), {
            type: 'bar',
            data: {
                labels: budgetData.map(item => item.name),
                datasets: [
                    {
                        label: 'Plan Budget',
                        data: budgetData.map(item => item.planned),
                        backgroundColor: '#3498db'
                    },
                    {
                        label: 'Actual Budget',
                        data: budgetData.map(item => item.actual),
                        backgroundColor: '#2ecc71'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return  value.toLocaleString() + " SR";
                            }
                        }
                    }
                }
            }
        });

        // Show error message if any
        <?php if (isset($error)): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo $error; ?>',
            icon: 'error',
            confirmButtonColor: '#2c3e50'
        });
        <?php endif; ?>
    </script>
</body>
</html> 