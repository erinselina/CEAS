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
    <title>Register Employee | Admin</title>
    <link rel="stylesheet" href="CSS/register_employee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="login-page">
        <h2 class="logo">CEAS</h2>
        <h3 class="description">Cubar Employee Attendance System</h3>
        <div class="form">
            <form class="reg_employee_form" action="process_emp.php" method="POST">
                <label for="first-name">First name:</label><br>
                <input type="text" id="first-name" name="first-name" required><br><br>

                <label for="last-name">Last name:</label><br>
                <input type="text" id="last-name" name="last-name" required><br><br>

                <label for="email">E-Mail:</label><br>
                <input type="email" id="email" name="email" required><br><br>

                <label for="phone-number">Phone Number:</label><br>
                <input type="text" id="phone-number" name="phone-number" required><br><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>


                <?php
                include 'db.php';

                // Get list of positions
                $sql = "SELECT id, role_name FROM roles";
                $result = $conn->query($sql);
                ?>

                <label for="position">Position:</label><br>
                <select id="position" name="position" required>
                    <option value="">-- Select Position --</option>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['role_name']) . "</option>";
                        }
                    } else {
                        echo "<option disabled>No roles available</option>";
                    }
                    ?>

                </select><br><br>

                <button type="submit" value="Submit"> Register</button><br><br>
                <button type="reset"> Reset</button>
            </form>
        </div>
    </div>
</body>

</html>