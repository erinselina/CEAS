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
    <title>Absent Shift</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/view_absent.css">
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
                    <h2>Absent Shift</h2>

                    <!-- HTML Table to Leave Application infos -->
                    <table class="absent-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1;">
                            <td>ID</td>
                            <td>Date</td>
                            <td>Start Time</td>
                            <td>End Time</td>
                            <td>Supporting Document</td>
                            <td>Absent Status</td>
                            <td style="text-align: center;">Action</td>
                        </tr>
                        <tr>
                            <?php
                            // Include database connection
                            include 'db.php';

                            // Get the currently logged-in user's ID from the session
                            $user_id = $_SESSION['user_id']; // Assuming 'user_id' is stored in the session after login

                            // Corrected query to fetch timecard records with staff name, and filtering by the logged-in user's ID
                            $sql = "SELECT * FROM absences WHERE user_id = ? ORDER BY absences.date DESC";

                            // Prepare and bind the query to prevent SQL injection
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id); // 'i' for integer

                            // Execute the query
                            $stmt->execute();

                            // Get the result
                            $result = $stmt->get_result();

                            // Loop through each row and display in the table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['schedule_time_in']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['schedule_time_out']) . "</td>";
                                echo "<td>" . (!empty($row['support_doc']) ? "<a href='" . htmlspecialchars($row['support_doc']) . "' target='_blank'>View</a>" : "No File") . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                            <button class='btn btn-warning btn-sm' onclick='openEditModal(" . $row['id'] . ")'>View</button>
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

    <!-- Modal To Edit Absent Record -->
    <div id="view_absence" class="modal">
        <div class="view-modal-content">
            <span class="close-btn" onclick="closeModal('view_absence')">&times;</span>
            <h2>Edit Absent Details</h2>
            <form class="editform" method="POST" action="update_absence_staff.php" enctype="multipart/form-data">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit-id" name="id">

                    <tr><!-- Date -->
                        <th style="text-align: left"><label for="date">Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit-date" name="date" readonly></th>
                    </tr>

                    <tr><!-- Time In -->
                        <th style="text-align: left"><label for="schedule_time_in">Time In:</label></th>
                        <th style="padding: 10px"><input type="time" id="edit-schedule_time_in" name="schedule_time_in" readonly></th>
                    </tr>

                    <tr><!-- Time Out -->
                        <th style="text-align: left; width: 30%"><label for="schedule_time_out">Time Out:</label></th>
                        <th style="padding: 10px"> <input type="time" id="edit-schedule_time_out" name="schedule_time_out" readonly></th>
                    </tr>

                    <tr><!-- Reason -->
                        <th style="text-align: left"><label for="reason">Reason:</label></th>
                        <th style="padding: 10px"> <input type="text" id="edit-reason" name="reason" required></th>
                    </tr>

                    <tr><!-- Supporting doc -->
                        <th style="text-align: left"><label for="document">Supporting Document: (Upload if applicable)</label></th>
                        <th style="padding: 10px">
                            <span id="file-info"></span><br><br>
                            <input style="text-align: left;" type="file" id="edit-support_doc" name="support_doc" class="file-upload" accept=".pdf,.jpg,.png">
                        </th>
                    </tr>
                </table><br>
                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        //function to close modal
        function closeModal() {
            document.getElementById("view_absence").style.display = "none";
        }

        // function to edit absent record
        function openEditModal(id) {
            fetch('get_absence_details_by_id.php?id=' + id)
                .then(response => response.json()) // Make sure it's parsed as JSON
                .then(data => {
                    console.log("Fetched Data:", data); // Log the fetched data for debugging

                    if (data.error) {
                        // Handle case when there's an error, like no data found
                        alert(data.error);
                        return;
                    }

                    // Populate modal fields if data is valid
                    document.getElementById("edit-id").value = id;
                    document.getElementById("edit-date").value = data.date;
                    document.getElementById("edit-schedule_time_in").value = data.schedule_time_in;
                    document.getElementById("edit-schedule_time_out").value = data.schedule_time_out;
                    document.getElementById("edit-reason").value = data.reason;

                    if (data.support_doc) {
                        document.getElementById("file-info").innerHTML = `<a href="/absent_attachment/${data.support_doc}" target="_blank">View File</a>`;
                    }

                    // Show the modal
                    document.getElementById("view_absence").style.display = "block";
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

</body>

</html>