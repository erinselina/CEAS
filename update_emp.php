<?php
include 'db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone-number'];
    $position = $_POST['position'];

    $query = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, position=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone_number, $position, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Employee details updated successfully.'); window.location.href='view_employee.php';</script>";
    } else {
        echo "<script>alert('Error updating employee details.');</script>";
    }
}

$conn->close();
?>
