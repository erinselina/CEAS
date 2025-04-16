<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
    $upload_dir = "images/"; // Directory to store images
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $upload_dir . time() . "_" . $file_name; // Unique filename

    // Allow only certain file types
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_types)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update profile picture path in database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Profile Picture Updated'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Error Uploading File. Try Again '); window.location.href='profile.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF allowed'); window.location.href='profile.php';</script>";
    }
}

$conn->close();
?>
