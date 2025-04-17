<?php
require_once '../php/php_backup.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Update status in pending_coaches table
    $conn->query("UPDATE pending_coaches SET status = 'rejected' WHERE id = $id");
}
?>
