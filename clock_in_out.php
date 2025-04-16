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

$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : "images/default_img.png";
$first_name = $user['first_name'];
$last_name = $user['last_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Employee | Admin</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/clock_in_out.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    // Include database connection
    include 'db.php';

    // Query to get positions from the database
    $sql = "SELECT id, role_name FROM roles";
    $result = $conn->query($sql);

    ?>

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
                    <h2 style="text-align: left;">Clock In & Out</h2>
                    <p class="system_time" id="time"></p>
                    <p class="system_date" id="datetime"></p>
                    <button onclick="clockIn()" class="punchIN_button">Clock In</button><br><br>
                    <button onclick="clockOut()" class="punchOUT_button">Clock Out</button>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    function updateTime() {
        let now = new Date(); // Gets current date and time from user's machine
        let timeString = now.toLocaleTimeString(); // Converts it to a readable format
        document.getElementById("time").innerText = timeString;
    }

    setInterval(updateTime, 1000); // Updates the time every second
    updateTime(); // Run initially

    //retrieve real-time date
    document.getElementById("datetime").innerHTML = new Date().toDateString();

    function clockIn() {
        let currentTime = new Date();

        // Format the date and time as YYYY-MM-DD and HH:MM:SS
        let date = currentTime.toISOString().split('T')[0];
        let time = currentTime.toTimeString().split(' ')[0];

        // Send the data to PHP using fetch
        fetch('clock_in.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `tc_date=${date}&tc_timeIN=${time}`
            })
            .then(response => response.text())
            .then(data => alert(data)); // Show response message
    }

    function clockOut() {
        let currentTime = new Date();

        // Format the date and time as YYYY-MM-DD and HH:MM:SS
        let date = currentTime.toISOString().split('T')[0];
        let time = currentTime.toTimeString().split(' ')[0];

        fetch('clock_out.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `tc_date=${date}&tc_timeOUT=${time}`
            })
            .then(response => response.text())
            .then(data => alert(data)); // Show response messagef
    }
</script>

</html>