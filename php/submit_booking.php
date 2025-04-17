<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coachconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$trainerName = $_POST['trainerName'];
$sport = $_POST['sport'];
$clientName = $_POST['clientName'];
$clientEmail = $_POST['clientEmail'];
$clientPhone = $_POST['clientPhone'];
$preferredDate = $_POST['preferredDate'];
$preferredTime = $_POST['preferredTime'];
$sessionType = $_POST['sessionType'];

// Insert booking into database
$sql = "INSERT INTO bookings (trainer_name, sport, client_name, client_email, client_phone, preferred_date, preferred_time, session_type) 
        VALUES ('$trainerName', '$sport', '$clientName', '$clientEmail', '$clientPhone', '$preferredDate', '$preferredTime', '$sessionType')";

if ($conn->query($sql) === TRUE) {
    // Redirect to thank you page after successful booking
    header("Location: ../html/thank_you.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>