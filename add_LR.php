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
    <title>Home</title>
    <link rel="stylesheet" href="CSS/add_LR.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div id="dashboard_main_container">
        <div class="dashboard_sidebar">
            <!-- Include the Sidebar -->
            <?php include 'sidebar_staff.php'; ?>
        </div>


        <div class="dashboard_content_container">
            <div class="dashboard_top_navigation">
                <a href="logout.php" id="logout_button"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <h3>Leave Request</h3>
                    <form action="process_LR.php" method="POST" enctype="multipart/form-data">
                        <label for="leave_type">Leave Type:</label><br>
                        <select id="leave_type" name="leave_type" required>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Emergency Leave">Emergency Leave</option>
                            <option value="Vacation Leave">Vacation Leave</option>
                        </select><br><br>

                        <label for="start_date">Start Date:</label><br>
                        <input type="date" name="start_date" id="start_date" required><br><br>

                        <label for="end_date">End Date:</label><br>
                        <input type="date" name="end_date" id="end_date" required><br><br>

                        <label for="num_days">No of Days:</label><br>
                        <input type="number" name="num_days" id="num_days" min="1" required><br><br>

                        <label for="reason">Reason:</label><br>
                        <textarea name="reason" id="reason" rows="3" required></textarea><br><br>

                        <label for="document">Supporting Document: (Upload if applicable)</label><br>
                        <input type="file" name="leave_doc" id="leave_doc" class="file-upload" accept=".pdf,.jpg,.png"><br><br>

                        <input type="submit" value="Submit">
                        <input type="reset">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>