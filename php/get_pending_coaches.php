<?php
require_once '../php/php_backup.php';

header('Content-Type: application/json');

// Fetch pending coach applications
$result = $conn->query("SELECT * FROM pending_coaches WHERE status = 'pending'");
$coaches = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($coaches);
?>
