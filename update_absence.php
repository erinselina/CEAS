<?php
include 'db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Double-check these column names match your DB exactly
    $query = "UPDATE absences SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Absence details reviewed'); window.location.href='view_emps_absences.php';</script>";
        } else {
            echo "<script>alert('No rows were updated. Check if the data actually changed or the record exists.');</script>";
        }
    } else {
        echo "<script>alert('Error updating attendance details: " . $stmt->error . "');</script>";
    }
    
}

$conn->close();
?>
