<?php
session_start();
include 'db.php'; // Include your database connection

if (isset($_GET['tc_id'])) {
    $tc_id = $_GET['tc_id'];
    $query = "DELETE FROM employee_timecard WHERE tc_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tc_id);
    $stmt->execute();
}

?>
