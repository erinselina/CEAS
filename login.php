<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; // Store user ID in session
            header("Location: home.php"); // Redirect to home page
            exit();
        } else {
            echo "<script>alert('Invalid Password, Please Try Again'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User Not Found'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-page">
    <h2 class="logo">CEAS</h2>
    <h3 class="description">Cubar Employee Attendance System</h3>
    <div class="form">
        <form class="login-form" action="" method="POST">
            <input type="email" name="email" placeholder="email" required/>
            <input type="password" name="password" placeholder="password" required/>
            <button type="submit" value="Login">login</button>
        </form>
    </div>
</div>
</body>
</html>