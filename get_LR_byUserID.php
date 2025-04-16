<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id']; // Logged-in user's ID

if (isset($_GET['leave_id'])) {
    $leave_id = $_GET['leave_id'];

    $stmt = $conn->prepare("SELECT * FROM leave_request WHERE leave_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $leave_id, $user_id);  // bind both leave_id and user_id
    $stmt->execute();
    $result = $stmt->get_result();

    if ($leave = $result->fetch_assoc()) {
        echo json_encode($leave);
    } else {
        echo json_encode(["error" => "Leave request not found or not authorized."]);
    }
}

$conn->close();
?>
