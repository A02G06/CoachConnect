<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['userId'])) {
        die("Invalid request.");
    }

    $userId = intval($data['userId']);
    $profileImage = "../users/" . $userId . ".jpg";

    if (file_exists($profileImage)) {
        if (unlink($profileImage)) {
            echo "Profile picture deleted successfully.";
        } else {
            echo "Failed to delete profile picture.";
        }
    } else {
        echo "Profile picture not found.";
    }
}
?>