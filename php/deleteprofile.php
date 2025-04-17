<?php
session_start();
include 'php_backup.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Not logged in.";
    exit;
}

$userId = $_SESSION['user_id'];

// Get the user's name
$query = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$name = $user['name'];

// Profile picture path
$profileImage = "../uploads/" . $name . ".jpg";

// Delete the profile picture if it exists
if (file_exists($profileImage)) {
    if (unlink($profileImage)) {
        echo "Profile picture deleted successfully.";
    } else {
        echo "Failed to delete profile picture.";
    }
} else {
    echo "No profile picture found to delete.";
}
?>