<?php
session_start();
include 'php_backup.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Not logged in.";
    exit;
}

$userId = $_SESSION['user_id'];

// Get name
$query = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$name = $user['name'];

// Handle uploaded image
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
    $uploadDir = "../uploads/";
    $targetFile = $uploadDir . $name . ".jpg"; // Use name for the filename

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFile)) {
        echo "Profile picture updated!";
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "Upload failed.";
}
?>