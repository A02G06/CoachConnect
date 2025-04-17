<?php
session_start();

// Database connection
require_once '../php/php_backup.php'; // Update this path to the correct file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Log all POST data
    error_log("POST Data: " . print_r($_POST, true));

    // Retrieve form data
    $trainer_id = $_POST['trainer_id'] ?? null;
    $client_name = $_POST['client_name'] ?? null;
    $client_email = $_POST['client_email'] ?? null;
    $client_phone = $_POST['client_phone'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $preferred_time = $_POST['preferred_time'] ?? null;
    $session_type = $_POST['sessionType'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    // Validate required fields
    if (!$trainer_id || !$client_name || !$client_email || !$client_phone || !$booking_date || !$preferred_time || !$session_type || !$user_id) {
        error_log("Missing Fields: trainer_id=$trainer_id, client_name=$client_name, client_email=$client_email, client_phone=$client_phone, booking_date=$booking_date, preferred_time=$preferred_time, sessionType=$session_type, user_id=$user_id");
        die("Error: Missing required fields. Please ensure all fields are filled.");
    }

    // Check if the trainer is available for the selected date and time
    $query = "SELECT COUNT(*) AS count 
              FROM client_bookings 
              WHERE trainer_id = ? AND booking_date = ? AND preferred_time = ? AND status = 'confirmed'";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error: Failed to prepare statement. " . $conn->error);
    }

    $stmt->bind_param("iss", $trainer_id, $booking_date, $preferred_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        die("Error: Trainer is unavailable for the selected date and time.");
    }

    // Insert the booking into the database
    $insert_query = "INSERT INTO client_bookings (trainer_id, user_id, client_name, client_email, client_phone, booking_date, preferred_time, session_type, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $insert_stmt = $conn->prepare($insert_query);

    if (!$insert_stmt) {
        die("Error: Failed to prepare statement. " . $conn->error);
    }

    $insert_stmt->bind_param("iissssss", $trainer_id, $user_id, $client_name, $client_email, $client_phone, $booking_date, $preferred_time, $session_type);

    if ($insert_stmt->execute()) {
        // Redirect to a thank-you page after successful booking
        header("Location: ../html/thank_you.html");
        exit;
    } else {
        die("Error: Could not process your booking. Please try again.");
    }
} else {
    die("Error: Invalid request method.");
}
?>
