<?php
include 'db.php';

if (isset($_POST['leave_id'])) {
    $leave_id = $_POST['leave_id'];
    $leave_req_status = $_POST['leave_req_status'];

    // Double-check these column names match your DB exactly
    $query = "UPDATE leave_request SET leave_req_status = ? WHERE leave_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $leave_req_status, $leave_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Leave request details reviewed'); window.location.href='view_emps_LR.php';</script>";
        } else {
            echo "<script>alert('No rows were updated. Check if the data actually changed or the record exists.');</script>";
        }
    } else {
        echo "<script>alert('Error updating attendance details: " . $stmt->error . "');</script>";
    }
    
}

$conn->close();
?>
