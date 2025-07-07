<?php
session_start();
require_once '../php/php_backup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if (!$booking_id || !$amount) {
        die("Invalid payment request.");
    }

    // Mark the booking as paid in the database
    $stmt = $conn->prepare("UPDATE client_bookings SET status = 'paid' WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        // Optionally, redirect to thank you page
        header("Location: ../html/thank_you.html");
        exit;
    } else {
        echo "<h2>Payment Failed</h2>
              <p>There was a problem processing your payment. Please try again.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: payment.php");
    exit;
}
?>