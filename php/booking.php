<?php
session_start();

// Database connection
require_once '../php/php_backup.php'; // Ensure this file exists and connects to the database
if (!$conn) {
    error_log("Database connection failed.");
    die("Error: Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Log session and POST data
    error_log("Session Data: " . print_r($_SESSION, true));
    error_log("POST Data: " . print_r($_POST, true));

    // Retrieve form data
    $trainer_id = $_POST['trainer_id'] ?? null;
    $trainer_name = $_POST['trainer_name'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $preferred_time = $_POST['preferred_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;
    $session_type = $_POST['session_type'] ?? null;
    $payment = $_POST['payment'] ?? 0;

    // Retrieve user details from session
    $client_name = $_SESSION['client_name'] ?? $_POST['client_name'] ?? null;
    $client_email = $_SESSION['client_email'] ?? $_POST['client_email'] ?? null;
    $client_phone = $_SESSION['client_phone'] ?? $_POST['client_phone'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    // Validate session
    if (!$user_id) {
        die("Error: User is not logged in.");
    }

    // Validate required fields
    if (empty($trainer_id)) {
        error_log("Error: Trainer ID is missing. POST Data: " . print_r($_POST, true));
        die("Error: Trainer ID is missing.");
    }
    if (empty($client_name)) {
        error_log("Error: Client name is missing. Session Data: " . print_r($_SESSION, true));
        die("Error: Client name is missing. Please ensure you are logged in or provide your name.");
    }
    if (empty($client_email)) {
        die("Error: Client email is missing.");
    }
    if (empty($client_phone)) {
        die("Error: Client phone is missing.");
    }
    if (empty($booking_date)) {
        die("Error: Booking date is missing.");
    }
    if (empty($preferred_time)) {
        die("Error: Preferred time is missing.");
    }
    if (empty($end_time)) {
        die("Error: End time is missing.");
    }
    if (empty($session_type)) {
        die("Error: Session type is missing.");
    }
    if (empty($payment)) {
        die("Error: Payment amount is missing.");
    }

    // Check if the trainer is available for the selected date and time
    $query = "SELECT COUNT(*) AS count 
              FROM client_bookings 
              WHERE trainer_id = ? AND booking_date = ? AND preferred_time = ? AND status = 'confirmed'";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        error_log("Error preparing availability query: " . $conn->error);
        die("Error: Failed to prepare statement.");
    }

    $stmt->bind_param("iss", $trainer_id, $booking_date, $preferred_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    error_log("Trainer availability result: " . print_r($row, true));

    if ($row['count'] > 0) {
        error_log("Trainer unavailable: trainer_id=$trainer_id, booking_date=$booking_date, preferred_time=$preferred_time");
        die("Error: Trainer is unavailable for the selected date and time.");
    }

    // Insert the booking into the database (include end_time and payment)
    $insert_query = "INSERT INTO client_bookings (trainer_id, user_id, client_name, client_email, client_phone, booking_date, preferred_time, end_time, session_type, payment, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $insert_stmt = $conn->prepare($insert_query);

    if (!$insert_stmt) {
        error_log("Error preparing insert query: " . $conn->error);
        die("Error: Failed to prepare statement.");
    }

    $insert_stmt->bind_param(
        "iisssssssi",
        $trainer_id,
        $user_id,
        $client_name,
        $client_email,
        $client_phone,
        $booking_date,
        $preferred_time,
        $end_time,
        $session_type,
        $payment
    );

    if ($insert_stmt->execute()) {
        // Save booking_id and amount in session for payment page
        $_SESSION['booking_id'] = $insert_stmt->insert_id;
        $_SESSION['payment_amount'] = $payment;

        // Redirect to payment page
        header("Location: payment.php");
        exit;
    } else {
        error_log("Error inserting booking: " . $conn->error);
        die("Error: Could not process your booking. Please try again.");
    }
} else {
    die("Error: Invalid request method.");
}
?>