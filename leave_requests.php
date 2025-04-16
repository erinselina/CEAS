<?php
session_start();
include 'db.php'; // Include your database connection

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $leave_id = $_GET['id'];

    if ($action == "edit") {
        // Fetch leave details from DB and display in a form
        $query = "SELECT * FROM leave_request WHERE leave_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $leave = $result->fetch_assoc();

        if ($leave) {
            echo "<h2>Edit Leave Request</h2>";
            echo "<form method='POST' action='leave_requests.php'>";
            echo "<input type='hidden' name='leave_id' value='" . $leave['leave_id'] . "'>";
            echo "Leave Type: <input type='text' name='leave_type' value='" . $leave['leave_type'] . "'><br>";
            echo "Start Date: <input type='date' name='start_date' value='" . $leave['leave_start_date'] . "'><br>";
            echo "End Date: <input type='date' name='end_date' value='" . $leave['leave_end_date'] . "'><br>";
            echo "<input type='submit' name='update_leave' value='Update'>";
            echo "</form>";
        }
    } elseif ($action == "delete") {
        // Delete leave request
        $query = "DELETE FROM leave_request WHERE leave_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $leave_id);

        if ($stmt->execute()) {
            echo "<script>alert('Leave request deleted successfully.'); window.location.href='view_LR.php';</script>";
        } else {
            echo "<script>alert('Error deleting leave request.');</script>";
        }
    } elseif ($action == "view") {
        // View leave request details
        $query = "SELECT * FROM leave_request WHERE leave_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $leave = $result->fetch_assoc();

        if ($leave) {
            echo "<h2>Leave Request Details</h2>";
            echo "Leave Type: " . $leave['leave_type'] . "<br>";
            echo "Start Date: " . $leave['leave_start_date'] . "<br>";
            echo "End Date: " . $leave['leave_end_date'] . "<br>";
            echo "Reason: " . $leave['leave_reason'] . "<br>";
            echo "Supporting Document: " . (!empty($leave['leave_doc']) ? "<a href='" . $leave['leave_doc'] . "' target='_blank'>View</a>" : "No File") . "<br>";
        }
    }
}

// Handle update form submission
if (isset($_POST['update_leave'])) {
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
