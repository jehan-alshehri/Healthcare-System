<?php
require_once 'db.php';

// Initialize variables for statistics
$stats = [
    'total_projects' => 0,
    'total_tasks' => 0,
    'total_team_members' => 0,
    'total_resources' => 0
];

try {
    $sql = "SELECT COUNT(*) as count FROM tbl_project";
    $stmt = $conn->query($sql);
    $stats['total_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $sql = "SELECT COUNT(*) as count FROM tbl_task";
    $stmt = $conn->query($sql);
    $stats['total_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $sql = "SELECT COUNT(*) as count FROM tbl_people";
    $stmt = $conn->query($sql);
    $stats['total_team_members'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $sql = "SELECT COUNT(*) as count FROM tbl_resources";
    $stmt = $conn->query($sql);
    $stats['total_resources'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $sql = "SELECT p.*, pe.name as project_manager_name 
            FROM tbl_project p 
            LEFT JOIN tbl_people pe ON p.project_manager_id = pe.id 
            ORDER BY p.plan_start_date DESC LIMIT 5";
    $stmt = $conn->query($sql);
    $recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Project Management System</title>
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

        .welcome-section {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            padding: 5rem 0;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.1;
        }

        .welcome-section h1 {
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .welcome-section .lead{
            font-size: 1.25rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .dashboard-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover .card-icon {
            transform: scale(1.1);
        }

        .card-title {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: var(--text-secondary);
            font-size: 1.25rem;
            font-weight: 500;
        }

        .section-title {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
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

        .quick-access-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .quick-access-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }

        .quick-access-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .quick-access-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .quick-access-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .recent-projects {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .recent-projects h4 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .project-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }

        .project-item:last-child {
            border-bottom: none;
        }

        .project-item:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .project-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .project-manager {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .project-dates {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .welcome-section {
                padding: 3rem 0;
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }
            
            .quick-access-card {
                margin-bottom: 1rem;
            }
        }
        .small-logo {
    max-width: 250px; /* أو أقل حسب ما تحب */
    height: auto;
    margin-top: 1rem;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="projects.php">Projects</a>
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

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Welcome to Healthcare PM</h1>
                    <p class="lead">Streamline your healthcare projects with our comprehensive management system. Track progress, manage resources, and collaborate effectively.</p>
                </div>
                <!-- بدل الكود الحالي الخاص باللوجو بهذا -->
<div class="col-lg-6 text-center">
    <img src="logo.png" alt="Healthcare Management" class="img-fluid rounded shadow-lg small-logo">
</div>

            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
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

        <!-- Quick Access Section -->
        <div class="row mb-4">
            <div class="col-md-3">
                <a href="projects.php" class="text-decoration-none">
                    <div class="quick-access-card">
                        <div class="quick-access-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="quick-access-title">New Project</div>
                        <div class="quick-access-description">Create a new project</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="text-decoration-none">
                    <div class="quick-access-card">
                        <div class="quick-access-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="quick-access-title">New Task</div>
                        <div class="quick-access-description">Add a new task</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="resources.php" class="text-decoration-none">
                    <div class="quick-access-card">
                        <div class="quick-access-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="quick-access-title">New Resource</div>
                        <div class="quick-access-description">Add a new resource</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="add_people.php" class="text-decoration-none">
                    <div class="quick-access-card">
                        <div class="quick-access-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="quick-access-title">New Team Member</div>
                        <div class="quick-access-description">Add a new team member</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="recent-projects">
            <h4>Recent Projects</h4>
            <?php if (!empty($recent_projects)): ?>
                <?php foreach ($recent_projects as $project): ?>
                    <div class="project-item">
                        <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                        <div class="project-manager">
                            Project Manager: <?php echo htmlspecialchars($project['project_manager_name'] ?? 'Not Assigned'); ?>
                        </div>
                        <div class="project-dates">
                            Plan Start: <?php echo date('M d, Y', strtotime($project['plan_start_date'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted">No recent projects found</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
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