<?php
session_start();
include 'db.php';

$month = isset($_GET['month']) ? $_GET['month'] : null;

$sql = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, COUNT(*) AS total_absences
        FROM absences a
        JOIN users u ON a.user_id = u.id
        WHERE a.status = 'Absent'";

if ($month) {
    $sql .= " AND MONTH(a.date) = ?";
}

$sql .= " GROUP BY a.user_id";

$stmt = $conn->prepare($sql);

if ($month) {
    $stmt->bind_param("i", $month);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
