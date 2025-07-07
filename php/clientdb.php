<?php
session_start(); // Ensure session is started

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coachconnect";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$userId = $_SESSION['user_id'];

$query = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle missing fields
$name = $user['name'] ?? 'Unknown';
$email = $user['email'] ?? 'Unknown';

// Profile image path
$profileImage = "../users/" . $userId . ".jpg"; // Use user ID for the image file
if (!file_exists($profileImage)) {
    $profileImage = "../Profileimg/default.jpg"; // fallback image
}

// Fetch booked sessions from client_bookings table for the logged-in user
$bookings_query = $conn->prepare("
    SELECT trainers.name AS trainer_name, client_bookings.booking_date, client_bookings.preferred_time, client_bookings.end_time, client_bookings.session_type, client_bookings.status 
    FROM client_bookings 
    JOIN trainers ON client_bookings.trainer_id = trainers.trainer_id 
    WHERE client_bookings.user_id = ?
");
if (!$bookings_query) {
    die("Error preparing bookings query: " . $conn->error);
}

$user_id = $_SESSION['user_id']; // Ensure this is set correctly

$bookings_query->bind_param("i", $user_id); // Ensure the correct user_id is passed
$bookings_query->execute();
$bookings_result = $bookings_query->get_result();

if (!$bookings_result) {
    die("Error fetching bookings: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-section, .bookings-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .profile-picture-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 10px;
            border: 2px solid #ddd;
        }
        .profile-picture-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .payment-status {
            font-weight: bold;
        }
        .paid {
            color: green;
        }
        .unpaid {
            color: red;
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
                    <li><a href="../php/home_loggedin.php">Home</a></li>
                    <li><a href="../html/aboutus.html">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Client Dashboard</h1>
        <div class="profile-section">
            <div class="profile-picture-container">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" class="profile-pic" alt="Profile Picture" onclick="viewProfilePicture()">
            </div>
            <input type="file" id="fileInput" style="display: none;" onchange="uploadProfilePicture()">

            <div class="details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>

            <div class="actions">
                <button onclick="triggerFileInput()">Upload Profile Picture</button>
                <button onclick="deleteProfilePicture()">Delete Profile Picture</button>
            </div>
        </div>
        
        <!-- Booked Sessions Section -->
        <div class="bookings-section">
            <h2>Your Booked Sessions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Trainer Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Session Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="bookingsTable">
                    <?php if ($bookings_result->num_rows === 0): ?>
                        <tr>
                            <td colspan="6">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = $bookings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                <td>
                                    <?php echo $row['preferred_time'] !== null ? htmlspecialchars($row['preferred_time']) : '<span style="color:#888;">N/A</span>'; ?>
                                </td>
                                <td>
                                    <?php echo $row['end_time'] !== null ? htmlspecialchars($row['end_time']) : '<span style="color:#888;">N/A</span>'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['session_type']); ?></td>
                                <td class="payment-status <?php echo $row['status'] === 'paid' ? 'paid' : 'unpaid'; ?>">
                                    <?php
                                        if ($row['status'] === 'paid') {
                                            echo '<span style="color:green;">Success</span>';
                                        } else {
                                            echo '<span style="color:red;">Pending</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; 2025 Coach Connect. All Rights Reserved.</p>
        </div>
    </footer>
<script>
    function triggerFileInput() {
        document.getElementById('fileInput').click();
    }

    function uploadProfilePicture() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("profilePic", file);
        formData.append("userId", <?php echo json_encode($userId); ?>); // Pass user ID to the server

        fetch("uploadprofile.php", {
            method: "POST",
            body: formData
        }).then(response => response.text())
          .then(data => {
              alert(data);
              location.reload(); // Refresh to show new profile pic
          });
    }

    function deleteProfilePicture() {
        if (confirm("Are you sure you want to delete your profile picture?")) {
            fetch("deleteprofile.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ userId: <?php echo json_encode($userId); ?> }) // Pass user ID to the server
            }).then(response => response.text())
              .then(data => {
                  alert(data);
                  location.reload(); // Refresh to show default profile pic
              });
        }
    }

    function viewProfilePicture() {
        const imgSrc = document.querySelector('.profile-pic').src;
        window.open(imgSrc, '_blank');
    }
</script>

</body>
</html>