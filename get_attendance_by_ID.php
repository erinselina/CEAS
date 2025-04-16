<?php
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user ID

// Query to fetch timecard records for the logged-in user
$sql = "SELECT tc_date, tc_timeIN, tc_timeOUT, tc_ttlHours
        FROM employee_timecard 
        WHERE user_id = ? 
        ORDER BY tc_date DESC"; // Sort by latest records

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>