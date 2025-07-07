<!-- filepath: c:\wamp64\www\CoachConnect\php\education.php -->
<?php
// Database connection
require_once '../php/php_backup.php'; // Ensure this file connects to the database

if (!$conn) {
    die("Error: Database connection failed.");
}

// Fetch only educational trainers from the database
$query = "SELECT trainer_id, name, expertise, phone, email FROM trainers WHERE category = 'education'";
$result = $conn->query($query);

if (!$result) {
    die("Error: Query failed. " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Trainers</title>
    <link rel="stylesheet" href="../css/education.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header-banner" style="background-image: url('../img/ed.jpg');">
        <div class="header-content">
            <h1>Educational Trainers</h1>
            <p>Get the best coaches and excel in your academic journey!</p>
        </div>
    </header>

    <section class="trainers-section">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for trainers..." onkeyup="filterTrainers()">
        </div>
        <div class="trainers-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $trainer_id = $row['trainer_id'];
                    $trainer_name = htmlspecialchars($row['name']);
                    $expertise = htmlspecialchars($row['expertise']);
                    $phone = htmlspecialchars($row['phone']);
                    $email = htmlspecialchars($row['email']);
                    $encoded_name = urlencode($trainer_name); // Encode the name for use in the URL
            ?>
            <div class="trainer">
                <img src="../img/trainer<?php echo $trainer_id; ?>.jpg" 
                     onerror="this.onerror=null; this.src='../img/default.jpg';" 
                     alt="Trainer <?php echo $trainer_id; ?>">
                <h3><?php echo $trainer_name; ?> - <?php echo $expertise; ?></h3>
                <p>Phone: <?php echo $phone; ?></p>
                <p>Email: <a href="mailto:<?php echo $email; ?>" style="color: blue;"><?php echo $email; ?></a></p>
                <button class="book-btn">
                    <a href="../html/booking.html?trainer_id=<?php echo $trainer_id; ?>&trainer=<?php echo $encoded_name; ?>">Book Now</a>
                </button>
            </div>
            <?php
                }
            } else {
                echo "<p>No educational trainers found.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: <a href="mailto:support@coachingplatform.com" style="color: blue;">support@coachingplatform.com</a></p>
                <p>Phone: <a href="tel:+919876543210">+91 98765 43210</a></p>
                <p>Address: 123, Main Street, Bangalore, India</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Feedback</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            © 2025 Coach Connect. All Rights Reserved.
        </div>
    </footer>

    <script>
        function filterTrainers() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const trainers = document.querySelectorAll('.trainer');
            trainers.forEach(trainer => {
                const text = trainer.textContent.toLowerCase();
                trainer.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>