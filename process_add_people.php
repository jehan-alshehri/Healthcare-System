<?php
require_once 'db.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');

    // Validate required fields
    if (empty($name)) {
        $response['errors'][] = "Name is required";
    }
    if (empty($email)) {
        $response['errors'][] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Invalid email format";
    }
    if (empty($phone)) {
        $response['errors'][] = "Phone number is required";
    }

    // If no validation errors, proceed with database insertion
    if (empty($response['errors'])) {
        try {
            // Prepare SQL statement
            $sql = "INSERT INTO tbl_people (name, email, address, phone) VALUES (:name, :email, :address, :phone)";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);

            // Execute the statement
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Person added successfully!";
            } else {
                $response['errors'][] = "Error adding person to database";
            }
        } catch(PDOException $e) {
            $response['errors'][] = "Database error: " . $e->getMessage();
        }
    }
}

// Redirect back to add_people.php with response
session_start();
$_SESSION['form_response'] = $response;
header("Location: add_people.php");
exit();
?> 