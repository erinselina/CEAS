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
    <title>List of Employees</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/view_employee.css">
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
                    <h2>View Employee</h2>
                    <button class="create_schedule_btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> Register Employee</button><br><br>

                    <!--Fetch Employee Details from-->
                    <?php ?>

                    <!-- HTML Table to Display Timecard Data -->
                    <table class="employee-table">
                        <tr style=" background-color: #2c3e50; color: #ecf0f1; padding: 15px;">
                            <td>ID</td>
                            <td>Profile Picture</td>
                            <td>Email</td>
                            <td>Phone Number</td>
                            <td>Position</td>
                            <td>Action</td>
                        </tr>
                        <tr>
                            <?php
                            // Include database connection
                            include 'db.php';

                            // Fetch all users from the database
                            $sql = "SELECT users.id, users.profile_picture, users.first_name, users.last_name, users.email, users.phone_number, roles.role_name 
                                    FROM users 
                                    LEFT JOIN roles ON users.position = roles.id"; // Joins roles table to get role name

                            $result = $conn->query($sql);

                            // Loop through each row and display in the table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td style='text-align: left;'>
                                        <img src='" . htmlspecialchars($row['profile_picture']) . "' alt='Profile Picture' width='50' height='50' style='border-radius:50%; vertical-align:middle; margin-right:10px;'>
                                        " . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "
                                    </td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role_name']) . "</td>"; // Fixed column

                                // Action Buttons (Fixed)
                                echo "<td style='text-align: center;'>
                                        <button class='btn btn-warning btn-sm' onclick='openEditModal(" . $row['id'] . ")'>Edit</button>
                                        <button class='btn btn-danger btn-sm' onclick='deleteEmployee(" . $row['id'] . ")'>Delete</button>

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

    <!-- Modal To Register New Employee-->
    <div id="reg_employee_modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('reg_employee_modal')">&times;</span>
            <h3>Register Employee</h3>
            <form class="reg_employee_form" id="reg_employee_form" action="process_emp.php" method="POST">
                <table style="width: 100%;">
                    <tr><!--Employee First Name-->
                        <th style="text-align: left"><label for="first-name">First name:</label></th>
                        <th style="padding: 10px"><input type="text" id="first-name" name="first-name" required></th>
                    </tr>
                    <tr><!--Employee Last Name-->
                        <th style="text-align: left"><label for="last-name">Last name:</label></th>
                        <th style="padding: 10px"><input type="text" id="last-name" name="last-name" required></th>
                    </tr>
                    <tr><!--Employee Email-->
                        <th style="text-align: left"><label for="email">E-Mail:</label></th>
                        <th style="padding: 10px"><input type="email" id="email" name="email" required></th>
                    </tr>
                    <tr><!--Employee Phone Number-->
                        <th style="text-align: left"><label for="phone-number">Phone Number:</label></th>
                        <th style="padding: 10px"><input type="text" id="phone-number" name="phone-number" required></th>
                    </tr>
                    <tr><!--Employee Initial Password-->
                        <th style="text-align: left"><label for="password">Password:</label></th>
                        <th style="padding: 10px"><input type="password" id="password" name="password" required></th>
                    </tr>
                    <tr><!--Employee Position-->
                        <th style="text-align: left"><label for="position">Position:</label></th>
                        <th style="padding: 10px">
                            <select id="position" name="position" style="width: 100%;" required>
                                <option value="">-- Select Position --</option>
                                <?php
                                // Include database connection
                                include 'db.php';

                                // Query to get positions from the database
                                $sql = "SELECT id, role_name FROM roles";
                                $result = $conn->query($sql);

                                // Loop through each row from the database
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['role_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </th>
                    </tr>
                </table><br>
                <button class="submit-btn" id="submit-btn" type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Modal To Edit Existing Employee -->
    <div id="edit_employee_modal" class="modal">
        <div class="edit-modal-content">
            <span class="close-btn" onclick="closeModal('edit_employee_modal')">&times;</span>
            <h2>Edit Employee</h2>
            <form class="editLRform" method="POST" action="update_emp.php">
                <table style="width: 100%;">
                    <input style="margin-bottom: 0;" type="hidden" id="edit-id" name="id">
                    <tr><!--Employee First Name-->
                        <th style="text-align: left"><label for="edit-first-name">First name:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit-first-name" name="first-name" required></th>
                    </tr>
                    <tr><!--Employee Last Name-->
                        <th style="text-align: left"><label for="edit-last-name">Last name:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit-last-name" name="last-name" required></th>
                    </tr>
                    <tr><!--Employee Email-->
                        <th style="text-align: left"><label for="edit-email">E-Mail:</label></th>
                        <th style="padding: 10px"><input type="email" id="edit-email" name="email" required></th>
                    </tr>
                    <tr><!--Employee Phone Number-->
                        <th style="text-align: left"><label for="edit-phone-number">Phone Number:</label></th>
                        <th style="padding: 10px"><input type="text" id="edit-phone-number" name="phone-number" required></th>
                    </tr>
                    <tr><!--Employee Position-->
                        <th style="text-align: left"><label for="edit-position">Position:</label></th>
                        <th style="padding: 10px">
                            <select id="edit-position" name="position" style="width: 100%;" required>
                                <option value="">-- Select Position --</option>
                                <?php
                                // Include database connection
                                include 'db.php';

                                // Query to get positions from the database
                                $sql = "SELECT id, role_name FROM roles";
                                $result = $conn->query($sql);

                                // Loop through each row from the database
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['role_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </th>
                    </tr>
                </table><br>
                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        // Open the schedule_modal modal
        function openModal() {
            document.getElementById("reg_employee_modal").style.display = "flex";
        }

        //function to close modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        // function to show the Edit Employee modal
        function openEditModal(id) {
            fetch('get_emp_details_by_id.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit-id").value = data.id;
                    document.getElementById("edit-first-name").value = data.first_name;
                    document.getElementById("edit-last-name").value = data.last_name;
                    document.getElementById("edit-email").value = data.email;
                    document.getElementById("edit-phone-number").value = data.phone_number;

                    // Set the correct position ID
                    document.getElementById("edit-position").value = data.position;

                    document.getElementById("edit_employee_modal").style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        //function to delete LR
        function deleteEmployee(id) {
            if (confirm("Are you sure you want to delete this leave employee?")) {
                fetch('delete_emp.php?id=' + id, {
                        method: 'GET'
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Employee deleted successfully!');
                        location.reload(); // Refresh page to reflect changes
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

</body>

</html>