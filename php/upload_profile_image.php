<?php
session_start();
include 'php_backup.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit();
}

$trainer_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $file = $_FILES['profile_image'];
    $fileName = basename($file['name']);
    $targetDir = "../uploads/";
    $targetFile = $targetDir . $fileName;

    // Optional: Validate file type
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        die("Only image files are allowed.");
    }

    // Move file and update database
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("UPDATE trainers SET profile_image = ? WHERE trainer_id = ?");
        $stmt->bind_param("si", $fileName, $trainer_id);
        $stmt->execute();
        $stmt->close();

        header("Location: ../php/dashboard-trainer.php");
        exit();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}
?>
