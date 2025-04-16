<?php
session_start();
include 'db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

?>
