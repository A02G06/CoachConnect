<?php
require_once '../php/php_backup.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Move coach details to trainers table
    $conn->query("INSERT INTO trainers (name, email, phone, expertise) 
                  SELECT name, email, phone, expertise FROM pending_coaches WHERE id = $id");

    // Update status in pending_coaches table
    $conn->query("UPDATE pending_coaches SET status = 'approved' WHERE id = $id");
}
?>
