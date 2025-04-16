<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user ID
$tc_date = $_POST['tc_date']; // Get date from JavaScript
$tc_timeIN = $_POST['tc_timeIN']; // Get time from JavaScript

// Insert clock-in record (prevent duplicate clock-ins)
$sql = "INSERT INTO employee_timecard (user_id, tc_date, tc_timeIN) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE tc_timeIN = VALUES(tc_timeIN)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $tc_date, $tc_timeIN);

if ($stmt->execute()) {
    echo "Clock-in successful at $tc_timeIN";
} else {
    echo "An error occurred. Please try again.";
}

$stmt->close();
$conn->close();
?>
