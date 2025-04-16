<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID
$error = $success = "";

//CHANGE PASSWORD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify current password
        if (!password_verify($current_password, $row['password'])) {
            $error = "Current password is incorrect!";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match!";
        } else {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in database
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                echo "<script>alert('Password changed successfully!'); window.location.href='profile.php';</script>";
            } else {
                echo "<script>alert('Error updating password. Try again!'); window.location.href='profile.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Unexpected Error. Please Try Again'); window.location.href='profile.php';</script>";
    }
}

?>