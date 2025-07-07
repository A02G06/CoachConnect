<?php
require_once '../php/php_backup.php'; // Ensure this connects to your database

if ($_GET['status'] === 'success') {
    $booking_id = $_GET['booking_id'] ?? null;

    if ($booking_id) {
        $stmt = $conn->prepare("UPDATE client_bookings SET status = 'success' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);

        if ($stmt->execute()) {
            header("Location: thank_you_dashboard.php");
            exit();
        } else {
            echo "Failed to update payment status.";
        }
    } else {
        echo "Invalid booking ID.";
    }
}
?>