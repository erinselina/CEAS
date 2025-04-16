<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT users.id, first_name, last_name, email, phone_number, position FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($employee = $result->fetch_assoc()) {
        echo json_encode($employee);
    } else {
        echo json_encode(["error" => "Employee not found"]);
    }
}

$conn->close();
?>
