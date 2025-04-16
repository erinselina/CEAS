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
    <title>Employees absences</title>
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
                    <?php include 'get_LR_by_ID.php'; ?>
                    <h2>Employees Absences</h2>

                    <!-- HTML Table to Leave Application infos -->
                    <table class="absent-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1;">
                            <td>ID</td>
                            <td>Employee</td>
                            <td>Date</td>
                            <td>Start Time</td>
                            <td>End Time</td>
                            <td>Reason</td>
                            <td>Absent Status</td>
                            <td style="text-align: center;">Action</td>
                        </tr>
                        <tr>
                            <?php
                            // Include database connection
                            include 'db.php';

                            // Corrected query to fetch timecard records with staff name
                            $sql = "SELECT absences.id, absences.date, absences.schedule_time_in, absences.schedule_time_out, absences.reason, absences.status, users.first_name, users.last_name
                                    FROM absences
                                    LEFT JOIN users ON absences.user_id = users.id";

                            $result = $conn->query($sql);

                            // Loop through each row and display in the table
                            while ($row = $result->fetch_assoc()) {
                                $full_name = htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); // Combine first and last name

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . $full_name . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['schedule_time_in']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['schedule_time_out']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                            <button class='btn btn-warning btn-sm' onclick='openModal(" . $row['id'] . ")'>View</button>
                                            <button class='btn btn-danger btn-sm' onclick='deleteAbsent(" . $row['id'] . ")'>Delete</button>
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

    <!-- Modal To Edit Existing Employee -->
    <div id="view_absence" class="modal">
        <div class="view-modal-content">
            <span class="close-btn" onclick="closeModal('view_absence')">&times;</span>
            <h2>View Absent Details</h2>
            <form class="editform" method="POST" action="update_absence.php">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit-id" name="id">

                    <tr><!-- Employee ID -->
                        <th style="text-align: left"><label for="employee">Employee ID:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit-employee" name="employee" readonly></th>
                    </tr>

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
                        <th style="padding: 10px"> <input type="text" id="edit-reason" name="reason" readonly></th>
                    </tr>

                    <tr><!-- Supporting doc -->
                        <th style="text-align: left"><label for="document">Supporting Document:</label></th>
                        <th style="padding: 10px"><span id="file-info"></span></th>
                    </tr>

                    <tr><!-- Approval Status -->
                        <th style="text-align: left"><label for="status">Approval Status:</label></th>
                        <th style="padding: 10px; width: 100%">
                            <select id="edit-status" name="status" value="Pending">
                                <option value="Pending">Pending</option>
                                <option value="ABSENT (with reason)">ABSENT (with reason)</option>
                                <option value="ABSENT">ABSENT</option>
                            </select>
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
        function openModal(id) {
            console.log("Opening modal for ID:", id);
            fetch('get_absence_details_by_id.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit-id").value = id;
                    document.getElementById("edit-employee").value = data.user_id;
                    document.getElementById("edit-date").value = data.date;
                    document.getElementById("edit-schedule_time_in").value = data.schedule_time_in;
                    document.getElementById("edit-schedule_time_out").value = data.schedule_time_out;
                    document.getElementById("edit-reason").value = data.reason;

                    if (data.support_doc) {
                        document.getElementById("file-info").innerHTML = `<a href="${data.support_doc}" target="_blank">View File</a>`;
                    }

                    document.getElementById("edit-status").value = data.status;

                    //display modal
                    document.getElementById("view_absence").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to delete absent record
        function deleteAbsent(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                fetch('delete_absent.php?id=' + id, {
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