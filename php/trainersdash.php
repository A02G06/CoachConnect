
<?php
session_start();
include '../php/php_backup.php';

// Check if the trainer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$trainer_id = $_SESSION['user_id']; // Get the logged-in trainer's ID

// Fetch trainer details from the database
$stmt = $conn->prepare("SELECT name, email, phone FROM trainers WHERE trainer_id = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $trainer = $result->fetch_assoc();
    $trainer_name = htmlspecialchars($trainer['name']);
    $trainer_email = htmlspecialchars($trainer['email']);
    $trainer_phone = htmlspecialchars($trainer['phone']);
} else {
    die("Trainer not found.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers Dashboard</title>
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-section, .resources-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .profile-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .resources-section input[type="file"] {
            margin-top: 10px;
        }
        .calendar-section {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .upload-success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../php/home_loggedin.php">
                    <img src="../img/logo.png" alt="Coach Connect Logo">
                </a>
            </div>
            <nav>
    <ul>
        <li><a href="../html/aboutus.html">About Us</a></li>
        <li><a href="../php/logout.php">Logout</a></li>
    </ul>
</nav>

        </div>
    </header>

    <div class="dashboard-container">
        <h1>Trainers Dashboard</h1>

        <!-- Profile Section -->
        <div class="profile-section">
            <h2>Trainer Profile</h2>

            <p>Name: <span id="trainerName"><?php echo $trainer_name; ?></span></p>
            <p>Email: <span id="trainerEmail"><?php echo $trainer_email; ?></span></p>
            <p>Phone: <span id="trainerPhone"><?php echo $trainer_phone; ?></span></p>
        </div>

        <!-- File Upload Section -->
        <div class="resources-section">
            <h2>Upload Resources</h2>
            <form action="../php/upload_resources.php" method="POST" enctype="multipart/form-data">
                <label for="resourceFile">Choose a file to upload:</label>
                <input type="file" id="resourceFile" name="resourceFile" required>
                <button type="submit" name="upload">Upload</button>
            </form>
            <p class="upload-success" id="uploadSuccess" style="display: none;">File uploaded successfully!</p>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <h2>Schedule</h2>
            <div id="calendar">
                <?php
                // Fetch bookings for the logged-in trainer
                $stmt = $conn->prepare("SELECT b.booking_date, u.name AS client_name
                                        FROM bookings b
                                        JOIN users u ON b.user_id = u.user_id
                                        WHERE b.trainer_id = ?
                                        ORDER BY b.booking_date ASC");
                $stmt->bind_param("i", $trainer_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<ul>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>Date: " . htmlspecialchars($row['booking_date']) . " - Client: " . htmlspecialchars($row['client_name']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No bookings scheduled.</p>";
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; 2025 Coach Connect. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Example: Show success message after file upload
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('upload') === 'success') {
            document.getElementById('uploadSuccess').style.display = 'block';
        }
    </script>
</body>
</html>