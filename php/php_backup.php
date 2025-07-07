<?php
// Move session_set_cookie_params() before session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, // 1 day
        'path' => '/',
        'domain' => '', // Set your domain if needed
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start(); // Start the session
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coachconnect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_trainer'])) {
    // Retrieve form data
    $trainer_id = intval($_POST['trainer_id'] ?? null);
    $user_id = $_SESSION['user_id'] ?? null;
    $client_name = $_POST['client_name'] ?? null;
    $client_email = $_POST['client_email'] ?? null;
    $client_phone = $_POST['client_phone'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $preferred_time = $_POST['preferred_time'] ?? null;
    $session_type = $_POST['session_type'] ?? null;
    $status = 'pending';

    // Validate input
    if (!isset($trainer_id, $user_id, $client_name, $client_email, $client_phone, $booking_date, $preferred_time, $session_type) ||
        empty($trainer_id) || empty($user_id) || empty($client_name) || empty($client_email) || empty($client_phone) || empty($booking_date) || empty($preferred_time) || empty($session_type)) {
        error_log("Missing Fields: trainer_id=$trainer_id, client_name=$client_name, client_email=$client_email, client_phone=$client_phone, booking_date=$booking_date, preferred_time=$preferred_time, session_type=$session_type, user_id=$user_id");
        die("Error: All fields are required.");
    }

    // Debugging: Log the values being inserted
    error_log("Trainer ID: $trainer_id");
    error_log("User ID: $user_id");
    error_log("Client Name: $client_name");
    error_log("Client Email: $client_email");
    error_log("Client Phone: $client_phone");
    error_log("Booking Date: $booking_date");
    error_log("Preferred Time: $preferred_time");
    error_log("Session Type: $session_type");
    error_log("Status: $status");

    // Insert booking into the database
    $insert_booking_query = $conn->prepare("INSERT INTO client_bookings (trainer_id, user_id, client_name, client_email, client_phone, booking_date, preferred_time, session_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_booking_query->bind_param("iisssssss", $trainer_id, $user_id, $client_name, $client_email, $client_phone, $booking_date, $preferred_time, $session_type, $status);

    if ($insert_booking_query->execute()) {
        echo "Booking successfully added!";
        header("Location: clientdb.php"); // Redirect to the client dashboard
        exit();
    } else {
        error_log("Error inserting booking: " . $conn->error);
        die("Error: Could not process your booking. Please try again.");
    }
}

// ✅ Insert Trainers Only If They Don't Exist
$check_trainers = $conn->query("SELECT COUNT(*) AS count FROM trainers");
$row = $check_trainers->fetch_assoc();

if ($row['count'] == 0) { // If no trainers exist, insert them
    $trainers = [
        ['Sarah Johnson', 'Football Coach', '+91 98765 43211', 'sarah.johnson@coachconnect.com', 'sports'],
        ['John Smith', 'Tennis Coach', '+91 98765 43212', 'john.smith@coachconnect.com', 'sports'],
        ['Michael Lee', 'Basketball Coach', '+91 98765 43213', 'michael.lee@coachconnect.com', 'sports'],
        ['David Carter', 'Swimming Coach', '+91 98765 43214', 'david.carter@coachconnect.com', 'sports'],
        ['Emma White', 'Gymnastics Coach', '+91 98765 43215', 'emma.white@coachconnect.com', 'sports'],
        ['Chris Martin', 'Boxing Coach', '+91 98765 43216', 'chris.martin@coachconnect.com', 'sports'],
        ['Olivia Brown', 'Yoga Instructor', '+91 98765 43217', 'olivia.brown@coachconnect.com', 'sports'],
        ['Emma White', 'Pilates Coach', '+91 98765 43218', 'emma.white.pilates@coachconnect.com', 'sports'],
        ['David Carter', 'Golf Coach', '+91 98765 43219', 'david.carter.golf@coachconnect.com', 'sports'],
        ['Daniel Wilson', 'Martial Arts Trainer', '+91 98765 43220', 'daniel.wilson@coachconnect.com', 'sports']
    ];

    $stmt = $conn->prepare("INSERT INTO trainers (name, expertise, phone, email, category) VALUES (?, ?, ?, ?, ?)");

    foreach ($trainers as $trainer) {
        $stmt->bind_param("sssss", $trainer[0], $trainer[1], $trainer[2], $trainer[3], $trainer[4]);
        $stmt->execute();
    }

    $stmt->close();
}

// ✅ Handle User Signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = trim($_POST['role']);
    $phone = trim($_POST['phone']);

    if ($role === 'new_coach') {
        $expertise = isset($_POST['expertise']) ? implode(', ', $_POST['expertise']) : null;

        if (empty($name) || empty($email) || empty($phone) || empty($expertise)) {
            die("Error: All fields are required for new coach registration.");
        }

        $stmt = $conn->prepare("INSERT INTO pending_coaches (name, email, phone, expertise) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $expertise);

        if ($stmt->execute()) {
            echo "Your application has been submitted for review.";
            header("Location: ../html/thank_you.html");
            exit();
        } else {
            die("Error: " . $stmt->error);
        }
    } else {
        $query = "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $email, $password, $role, $phone);

        if ($stmt->execute()) {
            header("Location: ../html/login.html");
            exit();
        } else {
            die("Error: " . $stmt->error);
        }
    }
}

// ✅ Handle User Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = trim($_POST['role']);

    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $db_role);
        $stmt->fetch();

        if ($role === 'coach') {
            $check_pending = $conn->prepare("SELECT COUNT(*) FROM pending_coaches WHERE email = ? AND status = 'pending'");
            $check_pending->bind_param("s", $email);
            $check_pending->execute();
            $check_pending->bind_result($pending_count);
            $check_pending->fetch();

            if ($pending_count > 0) {
                die("Error: Your application is under review. You cannot log in as a coach yet.");
            }
        }

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $db_role;
            $_SESSION['user_id'] = $user_id;

            if ($db_role === 'coach') {
                header("Location: ../php/trainersdash.php");
            } elseif ($db_role === 'client') {
                header("Location: ../php/clientdb.php");
            } else {
                echo "Invalid role.";
            }
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }

    $stmt->close();
}
?>