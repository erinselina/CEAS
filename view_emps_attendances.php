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
    <title>Employees Timecards</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/view_emps_attendances.css">
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
                    <h2>Employees Timecards</h2>

                    <!-- HTML Table to Display Timecard Data -->
                    <table class="attendances-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1; padding: 15px;">
                            <td>Employee</td>
                            <td>Date</td>
                            <td>Time In</td>
                            <td>Time Out</td>
                            <td>Total Hours</td>
                            <td>Action</td>
                        </tr>
                        <tr>
                            <?php
                            // Include database connection
                            include 'db.php';

                            // Corrected query to fetch timecard records with staff name
                            $sql = "SELECT employee_timecard.tc_id, employee_timecard.tc_date, employee_timecard.tc_timeIN, employee_timecard.tc_timeOUT, employee_timecard.tc_ttlHours, users.first_name, users.last_name
                                    FROM employee_timecard
                                    LEFT JOIN users ON employee_timecard.user_id = users.id
                                    ORDER BY employee_timecard.tc_date DESC";

                            $result = $conn->query($sql);

                            // Loop through each row and display in the table
                            while ($row = $result->fetch_assoc()) {
                                $full_name = htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); // Combine first and last name

                                echo "<tr>";
                                echo "<td>" . $full_name . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_timeIN']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_timeOUT']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tc_ttlHours']) . "</td>";

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                            <button class='btn btn-warning btn-sm' onclick='openEditModal(" . $row['tc_id'] . ")'>Edit</button>
                                            <button class='btn btn-danger btn-sm' onclick='deleteEmployee(" . $row['tc_id'] . ")'>Delete</button>
                                        </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal To Edit Employee Attendances-->
    <div id="edit_attendance" class="modal">
        <div class="edit-modal-content">
            <span class="close-btn" onclick="closeModal('edit_attendance')">&times;</span>
            <h2>Edit Attendance Details</h2>
            <form class="editform" method="POST" action="update_attendance.php">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit-tc_id" name="tc_id">

                    <tr><!-- Employee ID -->
                        <th style="text-align: left"><label for="employee">ID:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit-employee" name="employee" readonly></th>
                    </tr>
                    <tr><!-- Date Picker -->
                        <th style="text-align: left"><label for="schedule_date">Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit-schedule_date" name="schedule_date" required></th>
                    </tr>
                    <tr><!-- Time In -->
                        <th style="text-align: left"><label for="schedule_time_in">Time In:</label></th>
                        <th style="padding: 10px"><input type="time" id="edit-schedule_time_in" name="schedule_time_in" required></th>
                    </tr>
                    <tr><!-- Time Out -->
                        <th style="text-align: left"><label for="schedule_time_out">Time Out:</label></th>
                        <th style="padding: 10px"> <input type="time" id="edit-schedule_time_out" name="schedule_time_out" required></th>
                    </tr>
                </table><br>
                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        // Before submitting the form, add ":00" to the time values
        document.querySelector('form').addEventListener('submit', function(event) {
            // Get time inputs
            let timeIn = document.getElementById('edit-schedule_time_in').value;
            let timeOut = document.getElementById('edit-schedule_time_out').value;

            // Check if values are not empty and add ":00" to both
            if (timeIn) {
                document.getElementById('edit-schedule_time_in').value = timeIn + ":00";
            }

            if (timeOut) {
                document.getElementById('edit-schedule_time_out').value = timeOut + ":00";
            }
        });

        function openModal() {
            document.getElementById("reg_employee_modal").style.display = "flex";
        }

        //function to close modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        // function to show the Edit Employee modal
        function openEditModal(id) {
            fetch('get_attendance_details_by_id.php?tc_id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit-tc_id").value = id;
                    document.getElementById("edit-employee").value = data.user_id;
                    document.getElementById("edit-schedule_date").value = data.tc_date;
                    document.getElementById("edit-schedule_time_in").value = data.tc_timeIN;
                    document.getElementById("edit-schedule_time_out").value = data.tc_timeOUT;
                    document.getElementById("edit_attendance").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to delete LR
        function deleteEmployee(id) {
            if (confirm("Are you sure you want to delete this attendance record?")) {
                fetch('delete_attendance.php?tc_id=' + id, {
                        method: 'GET'
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Record deleted successfully!');
                        location.reload(); // Refresh page to reflect changes
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

</body>

</html>