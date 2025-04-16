<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $schedule_time_in = $_POST['time_in'];
    $schedule_time_out = $_POST['time_out'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : 'No reason provided';

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Update the schedule table
        $stmt = $conn->prepare("UPDATE schedule SET schedule_time_in= ?, schedule_time_out= ?, status = 'ABSENT' WHERE user_id = ? AND schedule_date = ?");
        $stmt->bind_param("ssis", $schedule_time_in, $schedule_time_out, $user_id, $date);
        $stmt->execute();

        // 2. Insert into absences table
        $stmt2 = $conn->prepare("INSERT INTO absences (user_id, date, schedule_time_in, schedule_time_out, reason, status) VALUES (?, ?, ?, ?, ?, 'Absent')");
        $stmt2->bind_param("issss", $user_id, $date, $schedule_time_in, $schedule_time_out, $reason);
        $stmt2->execute();

        // Commit if both queries succeed
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Marked as absent and recorded in absences.']);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error
        echo json_encode(['success' => false, 'message' => 'Failed to mark absent.', 'error' => $e->getMessage()]);
    }

    $stmt->close();
    $stmt2->close();
    $conn->close();
}
