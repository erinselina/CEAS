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
    <title>View Leave Request</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/view_LR.css">
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
                    <h2>Leave Request Information</h2>
                    <button class="submit_LR_btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> Submit New Application</button>

                    <!-- HTML Table to Leave Application infos -->
                    <table class="lr-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1;">
                            <td>ID</td>
                            <td>Type of Leave</td>
                            <td>Start Date</td>
                            <td>End Date</td>
                            <td>Number of Days</td>
                            <td>Reason</td>
                            <td>Supporting Document</td>
                            <td>Status</td>
                            <td style="text-align: center;">Action</td>
                        </tr>
                        <tr>
                            <?php
                            include 'db.php';
                            
                            $user_id = $_SESSION['user_id'];
                            
                            $stmt = $conn->prepare("SELECT * FROM leave_request WHERE user_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['leave_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_start_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_end_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['no_of_days']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_reason']) . "</td>";
                                echo "<td>" . (!empty($row['leave_doc']) ? "<a href='" . htmlspecialchars($row['leave_doc']) . "' target='_blank'>View</a>" : "No File") . "</td>";
                                echo "<td>" . htmlspecialchars($row['leave_req_status']) . "</td>";

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                        <button class='btn btn-warning btn-sm' onclick='openEditModal(" . $row['leave_id'] . ")'>Edit</button>
                                        <button class='btn btn-danger btn-sm' onclick='deleteLeave(" . $row['leave_id'] . ")'>Delete</button>

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

    <!--CREATE LEAVE REQUEST MODAL-->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('createModal')">&times;</span>
            <h2>Add Leave Request</h2>
            <form class="createLRForm" action="process_LR.php" method="POST" enctype="multipart/form-data">
                <table style="width: 100%;">
                    <tr>
                        <th style="text-align: left"><label for="leave_type">Leave Type:</label></th>
                        <th style="padding: 10px">
                            <select id="leave_type" name="leave_type" required>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                            </select>
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="start_date">Start Date:</label></th>
                        <th style="padding: 10px"><input type="date" name="start_date" id="start_date" required></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="end_date">End Date:</label></th>
                        <th style="padding: 10px"><input type="date" name="end_date" id="end_date" required></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="num_days">No of Days:</label></th>
                        <th style="padding: 10px"><input type="number" name="num_days" id="num_days" min="1" required></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="reason">Reason:</label></th>
                        <th style="padding: 10px"><textarea name="reason" id="reason" rows="3" required></textarea></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="document">Supporting Document: (Upload if applicable)</label></th>
                        <th style="padding: 10px"><input type="file" name="leave_doc" id="leave_doc" class="file-upload" accept=".pdf,.jpg,.png"></th>
                    </tr>
                </table><br>
                <input class="submit-btn" id="submit-btn" type="submit" value="Submit"><br><br>
                <input class="reset-btn" type="reset">
            </form>
        </div>
    </div>

    <!--EDIT LEAVE REQUEST MODAL-->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Leave Request</h2>
            <form class="editLRform" method="POST" action="update_LR_staff.php">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit_leave_id" name="leave_id">
                    <tr>
                        <th style="text-align: left"><label for="edit_leave_type">Leave Type:</label></th>
                        <th style="padding: 10px">
                            <select id="edit_leave_type" name="leave_type">
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                            </select>
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="edit_start_date">Start Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit_start_date" name="start_date"></th>
                    </tr>
                    <tr>
                        <th style="text-align: left"><label for="edit_end_date">End Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="edit_end_date" name="end_date"></th>
                    </tr>
                </table><br>
                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>

    <!--VIEW LEAVE REQUEST MODAL-->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <h2>Leave Request Details</h2>
            <p><strong>Leave Type:</strong> <span id="view_leave_type"></span></p>
            <p><strong>Start Date:</strong> <span id="view_start_date"></span></p>
            <p><strong>End Date:</strong> <span id="view_end_date"></span></p>
            <p><strong>Reason:</strong> <span id="view_reason"></span></p>
            <p><strong>Supporting Document:</strong> <span id="view_doc"></span></p>
        </div>
    </div>


    <script>
        //function to show Create LR Modal
        function openModal() {
            document.getElementById("createModal").style.display = "block";
        }


        // function to show the Edit LR modal
        function openEditModal(leave_id) {
            fetch('get_LR_byUserID.php?leave_id=' + leave_id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit_leave_id").value = data.leave_id;
                    document.getElementById("edit_leave_type").value = data.leave_type;
                    document.getElementById("edit_start_date").value = data.leave_start_date;
                    document.getElementById("edit_end_date").value = data.leave_end_date;
                    document.getElementById("editModal").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to View LR modal
        function openViewModal(leave_id) {
            fetch('get_LR_byUserID.php?id=' + leave_id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("view_leave_type").innerText = data.leave_type;
                    document.getElementById("view_start_date").innerText = data.leave_start_date;
                    document.getElementById("view_end_date").innerText = data.leave_end_date;
                    document.getElementById("view_reason").innerText = data.leave_reason;
                    document.getElementById("view_doc").innerHTML = data.leave_doc ?
                        `<a href="${data.leave_doc}" target="_blank">View</a>` :
                        "No File";
                    document.getElementById("viewModal").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to delete LR
        function deleteLeave(leaveId) {
            if (confirm("Are you sure you want to delete this leave request?")) {
                fetch('leave_requests.php?action=delete&id=' + leaveId, {
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

        //function to close modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }
    </script>

</body>

</html>