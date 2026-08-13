<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    // Check if the request is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input data
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $address = sanitize_input($_POST['address'] ?? '');

        // Validate required fields
        if (empty($name)) {
            throw new Exception('Name is required');
        }
        if (empty($email)) {
            throw new Exception('Email is required');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Prepare SQL statement
        $sql = "INSERT INTO tbl_people (name, email, phone, address) 
                VALUES (:name, :email, :phone, :address)";
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        // Execute the statement
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Team member added successfully!";
        } else {
            throw new Exception('Error adding team member');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?> 