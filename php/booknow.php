<!-- filepath: c:\xampp\htdocs\CoachConnect\php\booknow.php -->
<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: ../html/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - Coach Connect</title>
    <link rel="stylesheet" href="../css/booknow.css">
</head>
<body>
    <header>
        <h1>Our Trainers</h1>
    </header>

    <section class="intro">
        <p>At <strong>Coach Connect</strong>, we bring together expert trainers in Sports, Music, and Education to help you learn and grow. Whether you're looking for a **fitness coach, a music mentor, or an academic tutor**, we’ve got you covered! Explore our categories below and book your session today.</p>
    </section>

    <section class="categories">
        <div class="category">
            <img src="../img/braden-collum-9HI8UJMSdZA-unsplash.jpg" alt="Sports Training">
            <h2>Sports Training</h2>
            <p>Enhance your athletic skills with top-tier sports coaches. We offer training in football, basketball, tennis, and more.</p>
            <a href="../php/sports.php" class="btn">View Sports Trainers</a>
        </div>
       
        <div class="category">
            <img src="../img/wes-hicks-MEL-jJnm7RQ-unsplash.jpg" alt="Music Training">
            <h2>Music Training</h2>
            <p>Master your instrument or improve your singing with professional musicians. Piano, guitar, violin, vocals, and more!</p>
            <a href="../php/music.php" class="btn">View Music Trainers</a>
        </div>

        <div class="category">
            <img src="../img/susan-q-yin-2JIvboGLeho-unsplash.jpg" alt="Educational Coaching">
            <h2>Educational Coaching</h2>
            <p>Boost your academic performance with expert tutors in Math, Science, Languages, and Competitive Exams.</p>
            <a href="../php/education.php" class="btn">View Education Trainers</a>
        </div>
    </section>
</body>
</html>