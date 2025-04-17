<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coachconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch booking history
$query = "
    SELECT t.name AS coach_name, b.booking_date, b.session_type, b.status 
    FROM bookings b
    JOIN trainers t ON b.trainer_id = t.id
    WHERE b.user_id = ? AND b.booking_date < NOW()
    ORDER BY b.booking_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = [
        'coach_name' => $row['coach_name'],
        'date' => date('Y-m-d', strtotime($row['booking_date'])),
        'time' => date('H:i', strtotime($row['booking_date'])),
        'session_type' => $row['session_type'],
        'status' => $row['status']
    ];
}

echo json_encode($history);

$stmt->close();
$conn->close();
?>