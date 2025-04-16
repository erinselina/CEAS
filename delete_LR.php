<?php
session_start();
include 'db.php'; // Include your database connection

if (isset($_GET['leave_id'])) {
    $leave_id = $_GET['leave_id'];
    $query = "DELETE FROM leave_request WHERE leave_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
}

?>
