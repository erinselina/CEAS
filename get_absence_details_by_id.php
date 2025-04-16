<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT absences.user_id, date, schedule_time_in, schedule_time_out, reason, support_doc FROM absences WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($absent = $result->fetch_assoc()) {
        echo json_encode($absent);
    } else {
        // Debugging: Show what happens when no data is found
        echo json_encode(["error" => "Absent details not found"]);
    }
}

$conn->close();
?>
