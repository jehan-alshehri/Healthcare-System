<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'healthcare_mgmt');

try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $conn->exec("SET NAMES utf8mb4");
    
} catch(PDOException $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_connected() {
    global $conn;
    try {
        $conn->query("SELECT 1");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function get_total_records($table) {
    global $conn;
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM $table");
        return $stmt->fetch()['total'];
    } catch(PDOException $e) {
        return 0;
    }
}

function get_active_projects() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tbl_project WHERE actual_start_date IS NOT NULL");
        return $stmt->fetch()['total'];
    } catch(PDOException $e) {
        return 0;
    }
}

function get_completed_tasks() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tbl_task WHERE task_actual_start_date IS NOT NULL");
        return $stmt->fetch()['total'];
    } catch(PDOException $e) {
        return 0;
    }
}

function get_team_members() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tbl_people");
        return $stmt->fetch()['total'];
    } catch(PDOException $e) {
        return 0;
    }
}
?> 