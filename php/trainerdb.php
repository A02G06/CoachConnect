<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header("Location: login.php");
    exit();
}

include 'php_backup.php';

// Fetch bookings for the logged-in trainer
$trainer_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT b.booking_id, b.client_name, b.client_email, b.booking_date, b.status 
    FROM bookings b
    INNER JOIN trainers t ON b.trainer_id = t.trainer_id
    WHERE t.trainer_id = ?
");
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard</title>
    <link rel="stylesheet" href="../css/clientdb.css"> <!-- Reuse clientdb theme -->
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Trainer</h1>
        <h2>Your Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['client_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>