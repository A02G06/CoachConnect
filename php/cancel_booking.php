<?php
session_start();
header('Content-Type: text/plain');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coachconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed");
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

$userId = $_SESSION['user_id'];
$bookingId = $_GET['id'] ?? null;

if (!$bookingId) {
    echo "Invalid booking ID";
    exit();
}

// Cancel the booking
$query = "UPDATE bookings SET status = 'canceled' WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $bookingId, $userId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "Booking canceled successfully";
} else {
    echo "Failed to cancel booking";
}

$stmt->close();
$conn->close();
?>