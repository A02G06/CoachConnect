<?php
require_once '../php/php_backup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_coach'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $expertise = isset($_POST['expertise']) ? implode(', ', $_POST['expertise']) : null;

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($expertise)) {
        die("Error: All fields are required.");
    }

    // Insert into pending_coaches table
    $stmt = $conn->prepare("INSERT INTO pending_coaches (name, email, phone, expertise) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $expertise);

    if ($stmt->execute()) {
        // Redirect to thank-you page for new coaches
        header("Location: ../html/thank_you_coach.html");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}
?>
