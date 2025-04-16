<?php
include 'db.php';

if (isset($_POST['leave_id'])) {
    $leave_id = $_POST['leave_id'];
    $leave_type = $_POST['leave_type'];
    $leave_start_date = $_POST['start_date'];
    $leave_end_date = $_POST['end_date'];

    $query = "UPDATE leave_request SET leave_type=?, leave_start_date=?, leave_end_date=? WHERE leave_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $leave_type, $leave_start_date, $leave_end_date, $leave_id);

    if ($stmt->execute()) {
        echo "<script>alert('Leave request updated successfully.'); window.location.href='leave_requests.php';</script>";
    } else {
        echo "<script>alert('Error updating leave request.');</script>";
    }
}

$conn->close();
?>
