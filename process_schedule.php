<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to use the system");
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['employee'];
    $schedule_date = $_POST['schedule_date'];
    $schedule_time_in = $_POST['schedule_time_in'];
    $schedule_time_out = $_POST['schedule_time_out'];

    // Insert into database
    $sql = "INSERT INTO schedule (user_id, schedule_date, schedule_time_in, schedule_time_out)
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $schedule_date, $schedule_time_in, $schedule_time_out);

    if ($stmt->execute()) {
        echo "<script>alert('Schedule'); window.location.href='make_schedule.php';</script>";
    } else {
        echo "<script>alert('Error creating schedule. Please try again'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
