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
    <title>Employees Leave Requests</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/view_emps_LR.css">
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
                    <h2>Employees Leave Requests</h2>

                    <!-- HTML Table to Leave Application infos -->
                    <table class="lr-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1;">
                            <td>ID</td>
                            <td>Employee Name</td>
                            <td>Type of Leave</td>
                            <td>Start Date</td>
                            <td>End Date</td>
                            <td>Number of Days</td>
                            <td>Leave Request Status</td>
                            <td style="text-align: center;">Action</td>
                        </tr>
                        <tr>
                            <?php
                            // Include database connection
                            include 'db.php';

                            // Fetch all users from the database
                            $sql = "SELECT leave_request.leave_id, leave_request.user_id, leave_request.leave_type, leave_request.leave_start_date, 
                                    leave_request.leave_end_date, leave_request.no_of_days, leave_request.leave_req_status,
                                    users.first_name, users.last_name
                                    FROM leave_request 
                                    LEFT JOIN users ON leave_request.user_id = users.id"; // Joins roles table to get role name

                            $result = $conn->query($sql);

                            while ($row = $result->fetch_assoc()) {
                                $full_name = htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); // Combine first and last name

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['leave_id']) . "</td>";
                                echo "<td>" . $full_name . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_start_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_end_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['no_of_days']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_req_status']) . "</td>";

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                        <button class='btn btn-warning btn-sm' onclick='openViewModal(" . $row['leave_id'] . ")'>View</button>
                                        <button class='btn btn-danger btn-sm' onclick='deleteLeave(" . $row['leave_id'] . ")'>Delete</button>

                                    </td>";

                                echo "</tr>";
                            }
                            ?>
                        </tr>

                    </table>

                    <?php
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!--EDIT LEAVE REQUEST MODAL-->
    <div id="view_LR" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('view_LR')">&times;</span>
            <h2>Leave Request Details</h2>
            <form class="editLRform" method="POST" action="update_LR.php">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit_leave_id" name="leave_id">
                    <tr>
                        <th style="text-align: left"><label for="edit_leave_type">Leave Type:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit_leave_type" name="leave_type" readonly></th>
                    </tr>

                    <tr>
                        <th style="text-align: left"><label for="edit_start_date">Start Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit_leave_start_date" name="leave_start_date" readonly></th>
                    </tr>

                    <tr>
                        <th style="text-align: left"><label for="edit_end_date">End Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit_leave_end_date" name="leave_end_date" readonly></th>
                    </tr>

                    <tr>
                        <th style="text-align: left"><label for="num_days">No of Days:</label></th>
                        <th style="padding: 10px"><input type="number" id="edit_no_of_days" name="no_of_days" min="1" readonly></th>
                    </tr>

                    <tr>
                        <th style="text-align: left"><label for="reason">Reason:</label></th>
                        <th style="padding: 10px"><textarea name="reason" id="edit_leave_reason" name="leave_reason" rows="3" readonly></textarea></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="document">Supporting Document:</label></th>
                        <th style="padding: 10px"><a href="#" id="edit_leave_doc" target="_blank" style="font-weight: normal; text-align: left;">No document</a></th>
                    </tr>
                    <tr><!-- Approval Status -->
                        <th style="text-align: left"><label for="approval_status">Approval Status:</label></th>
                        <th style="padding: 10px; width: 100%">
                            <select id="edit_leave_req_status" name="leave_req_status" value="PENDING">
                                <option value="PENDING">Pending</option>
                                <option value="APPROVED">Approved</option>
                                <option value="DECLINED">Declined</option>
                            </select>
                        </th>
                    </tr>
                </table><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        //function to approve LR
        function openViewModal(leave_id) {
            fetch('get_LR_details_by_id.php?leave_id=' + leave_id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit_leave_id").value = leave_id;
                    document.getElementById("edit_leave_type").value = data.leave_type;
                    document.getElementById("edit_leave_start_date").value = data.leave_start_date;
                    document.getElementById("edit_leave_end_date").value = data.leave_end_date;
                    document.getElementById("edit_no_of_days").value = data.no_of_days;
                    document.getElementById("edit_leave_reason").value = data.leave_reason;
                    document.getElementById("edit_leave_doc").href = data.leave_doc;
                    document.getElementById("edit_leave_doc").textContent = data.leave_doc.split('/').pop(); 
                    document.getElementById("view_LR").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to close modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        //function to delete LR
        function deleteLeave(leave_id) {
            if (confirm("Are you sure you want to delete this leave request?")) {
                fetch('delete_LR.php?leave_id=' + leave_id, {
                        method: 'GET'
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Leave request deleted successfully!');
                        location.reload(); // Refresh page to reflect changes
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

</body>

</html>