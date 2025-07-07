<!-- filepath: c:\wamp64\www\CoachConnect\php\trainers_list.php -->
<?php
// Database connection
require_once '../php/php_backup.php'; // Ensure this file connects to the database

if (!$conn) {
    die("Error: Database connection failed.");
}

// Fetch trainers from the database
$query = "SELECT trainer_id, name, expertise FROM trainers"; // Use trainer_id instead of id
$result = $conn->query($query);

if (!$result) {
    die("Error: Query failed. " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "<h1>Available Trainers</h1>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $trainer_id = $row['trainer_id']; // Use trainer_id
        $trainer_name = urlencode($row['name']); // Encode the name for use in the URL
        $expertise = htmlspecialchars($row['expertise']); // Sanitize output

        // Display trainer details with a "Book Now" link
        echo "<li>";
        echo "<strong>Name:</strong> " . htmlspecialchars($row['name']) . "<br>";
        echo "<strong>Expertise:</strong> $expertise<br>";
        echo "<a href='../html/booking.html?trainer_id=$trainer_id&trainer=$trainer_name'>Book Now</a>";
        echo "</li><hr>";
    }
    echo "</ul>";
} else {
    echo "<p>No trainers found.</p>";
}

$conn->close();
?>