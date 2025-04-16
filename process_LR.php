<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to apply for leave.");
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_type = $_POST['leave_type'];
    $leave_start_date = $_POST['start_date'];
    $leave_end_date = $_POST['end_date'];
    $no_of_days = $_POST['num_days'];
    $leave_reason = $_POST['reason'];

    // Handle file upload
    $leave_doc = NULL; // Default value

    if (isset($_FILES["leave_doc"]) && $_FILES["leave_doc"]["error"] == 0) {
        $upload_dir = "leave_app_attachment/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique file name
        $file_name = time() . "_" . basename($_FILES["leave_doc"]["name"]);
        $target_file = $upload_dir . $file_name;

        // Allowed file types
        $allowed_types = ['pdf', 'jpg', 'png'];
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["leave_doc"]["tmp_name"], $target_file)) {
                $leave_doc = $target_file; // Store file path
            } else {
                die("Error: Failed to upload file.");
            }
        } else {
            die("Error: Invalid file type. Only JPG, PNG, and PDF allowed.");
        }
    }

    // Insert into database
    $sql = "INSERT INTO leave_request (leave_type, leave_start_date, leave_end_date, no_of_days, leave_reason, leave_doc, leave_req_status, user_id)
            VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $leave_type, $leave_start_date, $leave_end_date, $no_of_days, $leave_reason, $leave_doc, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Leave request submitted successfully!'); window.location.href='view_LR.php';</script>";
    } else {
        echo "<script>alert('Error submitting leave request.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
