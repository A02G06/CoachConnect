<?php
session_start();
include 'php_backup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_id = intval($_POST['trainer_id']); // Trainer ID from the form
    $client_id = $_SESSION['user_id']; // Logged-in client's user ID
    $session_name = trim($_POST['session_name']); // Session name from the form
    $booking_date = date('Y-m-d'); // Current date
    $status = 'pending'; // Default status for a new booking

    // Insert the booking into the database
    $stmt = $conn->prepare("INSERT INTO bookings (trainer_id, user_id, booking_date, status) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("iiss", $trainer_id, $client_id, $booking_date, $status);
    if ($stmt->execute()) {
        echo "Booking successful!";
        header("Location: trainers_page.php"); // Redirect back to the trainers page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>