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
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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
    <title>Attendance History</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/attendance_history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div id="dashboard_main_container">
        <div class="dashboard_sidebar">
            <?php
            switch ($_SESSION['role']) {
                case 'Restaurant Manager':
                    include 'sidebar.php';
                    break;
                case 'Supervisor':
                    include 'sidebar_staff.php';
                    break;
                case 'Server':
                    include 'sidebar_staff.php';
                    break;
                case 'Barista':
                    include 'sidebar_staff.php';
                    break;
                case 'Kitchen Crew':
                    include 'sidebar_staff.php';
                    break;
            }
            ?>
        </div>


        <div class="dashboard_content_container">
            <div class="dashboard_top_navigation">
                <a href="logout.php" id="logout_button"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <?php include 'get_attendance_by_ID.php'; ?>
                    <h2>Attendance History</h2>
                    <!-- HTML Table to Display Timecard Data -->
                    <table class="attendance-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1; padding: 15px;">
                            <td>Date</td>
                            <td>Time In</td>
                            <td>Time Out</td>
                            <td>Total Hours</td>
                        </tr>
                        <tr>
                            <?php
                            // Loop through each row and display in the table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['tc_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_timeIN']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_timeOUT']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_ttlHours']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tr>
                    </table>

                    <?php
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>