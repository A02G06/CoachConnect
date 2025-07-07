<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['profilePic']) || !isset($_POST['userId'])) {
        die("Invalid request.");
    }

    $userId = intval($_POST['userId']);
    $uploadDir = "../users/";
    $uploadFile = $uploadDir . $userId . ".jpg";

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadFile)) {
        echo "Profile picture uploaded successfully.";
    } else {
        echo "Failed to upload profile picture.";
    }
}
?>