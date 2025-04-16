<?php
include 'db.php';

if (isset($_GET['leave_id'])) {
    $leave_id = $_GET['leave_id'];
    $stmt = $conn->prepare("SELECT leave_type, leave_start_date, leave_end_date, no_of_days, leave_reason, leave_doc, leave_req_status FROM leave_request WHERE leave_id = ?");
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($leave = $result->fetch_assoc()) {
        echo json_encode($leave);
    } else {
        echo json_encode(["error" => "Leave request not found"]);
    }
}

$conn->close();
?>
