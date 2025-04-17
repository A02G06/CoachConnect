<?php
require_once '../php/php_backup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_coach'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $expertise = trim($_POST['expertise']);
    $qualifications = trim($_POST['qualifications']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($expertise) || empty($qualifications)) {
        die("Error: All fields are required.");
    }

    // Insert into pending_coaches table
    $stmt = $conn->prepare("INSERT INTO pending_coaches (name, email, phone, expertise, qualifications) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $expertise, $qualifications);

    if ($stmt->execute()) {
        echo "Your application has been submitted for review.";
        header("Location: ../html/thank_you.html");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}
?>
