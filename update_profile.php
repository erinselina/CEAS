<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Update query
    $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone_number, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
