<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user ID

// Fetch user data from database
$sql = "SELECT users.*, roles.role_name FROM users 
        JOIN roles ON users.position = roles.id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Store user data in session
$_SESSION['role'] = $user['role_name'];

//retrive user details
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : "images/default_img.png";
$first_name = $user['first_name'];
$last_name = $user['last_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div id="dashboard_main_container">
        <div class="dashboard_sidebar">
            <!-- Include the Sidebar -->
            <?php
            if ($_SESSION['role'] === 'Restaurant Manager') {
                include 'sidebar.php';
            } else {
                include 'sidebar_staff.php';
            }
            ?>
        </div>


        <div class="dashboard_content_container">
            <div class="dashboard_top_navigation">
                <a href="logout.php" id="logout_button"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <div class="dashboard_content">
                <div class="dashboard_content_main" style="text-align: center;">
                &nbsp;&nbsp;
                    <h2>Home</h2>
                    <?php
                    if ($_SESSION['role'] === 'Restaurant Manager') {
                        // Admin Menu Buttons

                        echo '<button class="btn_punch" onclick="window.location.href=\'clock_in_out.php\'"><i style="font-size: 20px;" class="fa fa-clock"></i><br><br>Clock-in & Clock-out</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'make_schedule.php\'"><i style="font-size: 20px;" class="fa fa-calendar"></i><br><br>Make Schedule</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_LR.php\'"><i style="font-size: 20px;" class="fa-solid fa-pen"></i><br><br>Apply for Leave</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'profile.php\'"><i style="font-size: 20px;" class="fa-solid fa-circle-user"></i><br><br>Profile Setting</button><br><br>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_employee.php\'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>List of Employees</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_emps_attendances.php\'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Time Cards</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_emps_absences.php\'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Absent List</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_emps_LR.php\'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Leave Requests</button>';
                    } else {
                        // Staff Menu Buttons
                        echo '<button class="btn_punch" onclick="window.location.href=\'clock_in_out.php\'"><i style="font-size: 20px;" class="fa fa-clock"></i><br><br>Clock-in & Clock-out</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_schedule.php\'"><i style="font-size: 20px;" class="fa fa-calendar-check"></i><br><br>View Schedule</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'view_LR.php\'"><i style="font-size: 20px;" class="fa-solid fa-pen"></i><br><br>Apply for Leave</button>';
                        echo '<button class="btn_punch" onclick="window.location.href=\'profile.php\'"><i style="font-size: 20px;" class="fa-solid fa-circle-user"></i><br><br>Profile Setting</button>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>