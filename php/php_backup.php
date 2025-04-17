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



// Check if the bookings table exists
$check_table = $conn->query("SHOW TABLES LIKE 'bookings'");
if ($check_table->num_rows == 0) {
    // Create the bookings table if it doesn't exist
    $create_table_query = "
        CREATE TABLE bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trainer_id INT NOT NULL,
            user_id INT NOT NULL,
            booking_date DATE NOT NULL,
            session_type VARCHAR(50) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (trainer_id) REFERENCES trainers(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ";
    if (!$conn->query($create_table_query)) {
        die("Error creating bookings table: " . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_trainer'])) {
    $trainer_id = intval($_POST['trainer_id']); // Trainer ID from the form
    $user_id = $_SESSION['user_id']; // Logged-in user's ID
    $booking_date = $_POST['booking_date']; // Booking date from the form
    $session_type = $_POST['session_type']; // Session type from the form
    $status = 'pending'; // Default status

    // Validate input
    if (empty($trainer_id) || empty($user_id) || empty($booking_date) || empty($session_type)) {
        die("Error: All fields are required.");
    }

    // Debugging: Check the values being inserted
    echo "Trainer ID: $trainer_id<br>";
    echo "User ID: $user_id<br>";
    echo "Booking Date: $booking_date<br>";
    echo "Session Type: $session_type<br>";
    echo "Status: $status<br>";

    // Insert booking into the database
    $insert_booking_query = $conn->prepare("INSERT INTO bookings (trainer_id, user_id, booking_date, session_type, status) VALUES (?, ?, ?, ?, ?)");
    $insert_booking_query->bind_param("iisss", $trainer_id, $user_id, $booking_date, $session_type, $status);

    if ($insert_booking_query->execute()) {
        echo "Booking successfully added!";
        header("Location: clientdb.php"); // Redirect to the client dashboard
        exit();
    } else {
        die("Error inserting booking: " . $conn->error);
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
        ['Daniel Wilson', 'Martial Arts Trainer', '+91 98765 43220', 'daniel.wilson@coachconnect.com', 'sports'],
        ['Emily Davis', 'Piano Instructor', '+91 98765 43211', 'emily.davis@example.com', 'music'],
        ['James Wilson', 'Guitar Coach', '+91 98765 43212', 'james.wilson@example.com', 'music'],
        ['Olivia Roberts', 'Violin Instructor', '+91 98765 43213', 'olivia.roberts@example.com', 'music'],
        ['Daniel Smith', 'Drums Teacher', '+91 98765 43214', 'daniel.smith@example.com', 'music'],
        ['Ava Johnson', 'Vocal Coach', '+91 98765 43215', 'ava.johnson@example.com', 'music'],
        ['Chris Adams', 'Saxophone Instructor', '+91 98765 43216', 'chris.adams@example.com', 'music'],
        ['Sophia Martinez', 'Flute Trainer', '+91 98765 43217', 'sophia.martinez@example.com', 'music'],
        ['Ethan Brooks', 'Music Theory Tutor', '+91 98765 43218', 'ethan.brooks@example.com', 'music'],
        ['Liam Carter', 'Bass Guitar Trainer', '+91 98765 43219', 'liam.carter@example.com', 'music'],
        ['Isabella Reed', 'Choir Conductor', '+91 98765 43220', 'isabella.reed@example.com', 'music'],
        ['Dr. Lisa Carter', 'Advanced Mathematics', '+91 98765 43210', 'lisacarter@coaching.com', 'education'],
        ['Mark Reynolds', 'English Grammar', '+91 87654 32109', 'markreynolds@coaching.com', 'education'],
        ['Dr. Emily Foster', 'Science Educator', '+91 76543 21098', 'emilyfoster@coaching.com', 'education'],
        ['Thomas Greene', 'Test Prep (SAT, GRE, GMAT)', '+91 65432 10987', 'thomasgreene@coaching.com', 'education'],
        ['Priya Sharma', 'Coding (Python, Web Development, Data Science)', '+91 54321 09876', 'priyasharma@coaching.com', 'education'],
        ['John Matthews', 'Business (Accounting, Finance, Entrepreneurship)', '+91 54321 09876', 'johnmatthew@coaching.com', 'education'],
        ['Sophia Bennett', 'History and Political Science', '+91 43210 98765', 'sophiabennett@coaching.com', 'education'],
        ['Antonio Delgado', 'Language (Spanish & French)', '+91 32109 87654', 'antoniodelgado@coaching.com', 'education'],
        ['Rachel Anderson', 'Public Speaking', '+91 21098 76543', 'rachelanderson@coaching.com', 'education'],
        ['Michael Thompson', 'Special Education', '+91 09876 54321', 'michaelthompson@coaching.com', 'education']
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
        $expertise = trim($_POST['expertise']);
        $qualifications = trim($_POST['qualifications']);

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($expertise) || empty($qualifications)) {
            die("Error: All fields are required for new coach registration.");
        }

        // Insert into pending_coaches table
        $stmt = $conn->prepare("INSERT INTO pending_coaches (name, email, phone, expertise, qualifications) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $expertise, $qualifications);

        if ($stmt->execute()) {
            echo "Your application has been submitted for review.";
        } else {
            die("Error: " . $stmt->error);
        }
    } else {
        // Handle client registration
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
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

    // Fetch the hashed password and role from the database
    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $db_role);
        $stmt->fetch();

        if ($role === 'coach') {
            // Restrict login for new coaches
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
            // Set session variables for the logged-in user
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $db_role;
            $_SESSION['user_id'] = $user_id;

            // Redirect based on the user's role
            if ($db_role === 'coach') {
                header("Location: ../html/trainersdash.html");
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