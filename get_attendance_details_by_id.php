<?php
include 'db.php';

if (isset($_GET['tc_id'])) {
    $tc_id = $_GET['tc_id'];
    $stmt = $conn->prepare("SELECT employee_timecard.user_id, tc_date, tc_timeIN, tc_timeOUT FROM employee_timecard WHERE tc_id = ?");
    $stmt->bind_param("i", $tc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($timecard = $result->fetch_assoc()) {
        echo json_encode($timecard);
    }else{    
        echo json_encode(["error" => "Timecard not found"]);
    }
}

$conn->close();
?>
