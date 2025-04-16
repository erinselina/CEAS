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
    <title>Profile</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/profile3.css">
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
                    <h2>Profile</h2>
                    <div class="picture_part">
                        <table>
                            <tr>
                                <th class="column1">
                                    <img src="<?php echo $profile_picture; ?>" alt="User Image">
                                </th>
                                <th class="column2">
                                    <form action="upload_pfp.php" method="POST" enctype="multipart/form-data">
                                        <input class="chooseFile" type="file" name="profile_picture" accept="image/*" required><br>
                                        <button type="submit" class="upload_photo_button">Upload Photo</button>
                                    </form>
                                    <p style="font-style: italic; margin-top: 5px;">choose your file then press the upload button</p>
                                </th>
                            </tr>
                        </table>
                    </div>

                    <div class="personal_info">
                        <h3>Personal Information</h3>
                        <button class="edit-button" onclick="openModal()">Edit</button>
                        <table>
                            <tr>
                                <th style="padding-right: 4cm">
                                    <h4 style="text-align: left">First Name</h4>
                                    <p style="font-weight: normal; text-align: left"><?php echo htmlspecialchars($user['first_name']); ?></p>
                                </th>
                                <th style="padding-right: 4cm">
                                    <h4 style="text-align: center">Last Name</h4>
                                    <p style="font-weight: normal; text-align: center"><?php echo htmlspecialchars($user['last_name']); ?></p>
                                </th>
                                <th>
                                    <h4 style="text-align: center">Email</h4>
                                    <p style="font-weight: normal; text-align: center"><?php echo htmlspecialchars($user['email']); ?></p>
                                </th>
                                <th style="padding-left: 4cm">
                                    <h4 style="text-align: right">Phone Number</h4>
                                    <p style="font-weight: normal; text-align: right"><?php echo htmlspecialchars($user['phone_number']); ?></p>
                                </th>
                            </tr>
                        </table>
                    </div>

                    <!-- Modal To Personalize Personal Inforamtion (Hidden by Default) -->
                    <div id="editModal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closeModal()">&times;</span>
                            <h3>Edit Profile</h3>
                            <form id="editForm" action="update_profile.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <table style="width: 100%;">
                                    <tr>
                                        <th style="text-align: left"><label>First Name:</label></th>
                                        <th style="padding: 10px"><input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left"><label>Last Name:</label></th>
                                        <th style="padding: 10px"><input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left"><label>Email:</label></th>
                                        <th style="padding: 10px"><input type="email" name="email" value="<?php echo $user['email']; ?>" required></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left"><label>Phone Number:</label></th>
                                        <th style="padding: 10px"><input type="text" name="phone_number" value="<?php echo $user['phone_number']; ?>" required></th>
                                    </tr>
                                </table><br>
                                <button class="submit_changed_details" type="submit">Save Changes</button>
                            </form>
                        </div>
                    </div>

                    <div class="password_part">
                        <h3>Change Password</h3>
                        <button class="edit-button" onclick="openModal2()">Edit</button>
                        <p>******</p>
                    </div><br><br>

                    <!-- Modal To Change Password (Hidden by Default) -->
                    <div id="pswModal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closeModal2()">&times;</span>
                            <h3>Edit Password</h3>
                            <form id="pswForm" action="update_psw.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <table>
                                    <tr>
                                        <th style="text-align: left"><label>Current Password:</label></th>
                                        <th style="padding: 10px"><input type="password" name="current_password" required></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left"><label>New Password:</label></th>
                                        <th style="padding: 10px"><input type="password" name="new_password" required></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left"><label>Confirm New Password:</label></th>
                                        <th style="padding: 10px"><input type="password" name="confirm_password" required></th>
                                    </tr>
                                </table><br>
                                <button class="submit_changed_details" type="submit">Save Changes</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Open the personalize-profile modal
        function openModal() {
            document.getElementById("editModal").style.display = "flex";
        }

        // Open the change-password modal
        function openModal2() {
            document.getElementById("pswModal").style.display = "flex";
        }

        // Close the edit profile
        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

        // Close the change password
        function closeModal2() {
            document.getElementById("pswModal").style.display = "none";
        }
    </script>

</body>

</html>