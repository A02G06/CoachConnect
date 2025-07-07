<?php
session_start();
include 'php_backup.php';

// Debugging: Output session variables for inspection
error_log("Session Variables: " . print_r($_SESSION, true));

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    error_log("User not logged in. Redirecting to login page.");
    header("Location: ../html/login.html");
    exit();
}

// Retrieve user's name or email from the session
$userName = $_SESSION['name'] ?? $_SESSION['email'];

// Fetch upcoming bookings from the database
$upcomingBookings = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT t.name AS trainer_name, b.booking_date
        FROM bookings b
        INNER JOIN trainers t ON b.trainer_id = t.trainer_id
        WHERE b.user_id = ? AND b.booking_date >= CURDATE()
        ORDER BY b.booking_date ASC
        LIMIT 3
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $upcomingBookings[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Connect - Smart Scheduling</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .overlay-button {
            background: #007bff;
            color: #fff !important;
            border: none;
            padding: 12px 32px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .overlay-button:hover {
            background: #0056b3;
            color: #fff !important;
        }
        .welcome-banner h2,
        .welcome-banner p {
            color: #fff !important;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="home_loggedin.php">
                    <img src="../img/logo.png" alt="Coach Connect Logo">
                </a>
            </div>
            <div class="marquee-container">
                <span class="marquee-text">Welcome to Coach Connect!</span>
            </div>
            <nav>
                <ul>
                    <li><a href="../php/logout.php">Logout</a></li>
                    <li><a href="../php/clientdb.php">Dashboard</a></li>
                    <li><a href="../html/aboutus.html">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Personalized Welcome -->
    <section class="welcome-banner" style="text-align:center; margin: 30px 0 10px 0;">
        <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
        <p>We're glad to see you again. Ready for your next session?</p>
    </section>

     <section class="banner-slider">
        <div class="slider-container" id="sliderContainer">

            <div class="slide">
                <img src="../img/ai-trainer.jpg" alt="Empower Your Learning">
                <div class="slide-content">
                    <h2>Empower Your Learning</h2>
                    <p>Connect with expert coaches today.</p>
                </div>
            </div>
            <div class="slide">
                <img src="../img/braden-collum-9HI8UJMSdZA-unsplash.jpg" alt="Achieve Your Goals">
                <div class="slide-content">
                    <h2>Achieve Your Goals</h2>
                    <p>Personalized coaching for your success.</p>
                </div>
            </div>
            <div class="slide">
                <img src="../img/arbeitsgruppe-tisch-css-karriere-job-martin-regli-radwa-maria-13_image-16-9 (1).avif" alt="Unlock Your Potential">
                <div class="slide-content">
                    <h2>Unlock Your Potential</h2>
                    <p>Start your journey now.</p>
                </div>
            </div>
        </div>
        </section>

    <section class="main-content">
        <h2>Connect with Experienced Coaches</h2>
        <p>Book one-on-one appointments with specialized and general coaches to enhance your learning journey.</p>
        <a href="../php/booknow.php" class="overlay-button">Book Now</a>
    </section>

    <section class="why-choose">
        <h2>Why Choose Coach Connect?</h2>
        <p class="why-tagline">Your ultimate platform for expert coaching, flexible scheduling, and personalized growth!</p>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-user-tie"></i>
                <h3>Access to Top Coaches</h3>
                <p>Learn from industry-leading professionals who are experts in their field.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-clock"></i>
                <h3>Flexible Scheduling</h3>
                <p>Book coaching sessions at your convenience, anytime, anywhere.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Track Your Progress</h3>
                <p>Stay motivated with real-time performance tracking and growth insights.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-handshake"></i>
                <h3>Seamless Experience</h3>
                <p>Enjoy a user-friendly platform designed for smooth and hassle-free navigation.</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: <a href="mailto:support@coachingplatform.com">support@coachingplatform.com</a></p>
                <p>Phone: <a href="tel:+919876543210">+91 98765 43210</a></p>
                <p>Address: 123, Main Street, Bangalore, India</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../html/quicklink.html">FAQ</a></li>
                    <li><a href="../html/quicklink.html">Help</a></li>
                    <li><a href="../html/quicklink.html">Feedback</a></li>
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
            <p>&copy; 2025 Coach Connect. All Rights Reserved.</p>
        </div>
    </footer>
    <script>
  const sliderContainer = document.getElementById("sliderContainer");
  const slides = document.querySelectorAll(".slide");
  const totalSlides = slides.length;

  let currentIndex = 0;

  function moveToNextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
  }

  // Change slide every 5 seconds
  setInterval(moveToNextSlide, 2000);
</script>
</body>
</html>