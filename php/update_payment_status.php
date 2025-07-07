<?php
require_once '../php/php_backup.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($booking_id && $status) {
        $stmt = $conn->prepare("UPDATE client_bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $booking_id);

        if ($stmt->execute()) {
            echo "Payment status updated successfully.";
        } else {
            http_response_code(500);
            echo "Failed to update payment status.";
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo "Invalid request.";
    }
}
?>