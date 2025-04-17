<?php
// filepath: c:\xampp\htdocs\CoachConnect\php\upload_resources.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resourceFile'])) {
    $uploadDir = '../uploads/';
    $uploadFile = $uploadDir . basename($_FILES['resourceFile']['name']);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['resourceFile']['tmp_name'], $uploadFile)) {
        header("Location: ../html/trainersdash.html?upload=success");
        exit;
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}
?>