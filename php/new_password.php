<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['new_password'], $_POST['confirm_password'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'coachconnect');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Verify the token and expiry
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashedPassword, $token);
        $stmt->execute();

        echo "Your password has been updated successfully.";
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
}
?>
