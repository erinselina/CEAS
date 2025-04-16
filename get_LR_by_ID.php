<!--Get LR details by currently logged in user id-->
<?php
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve logged-in user ID

// Query to fetch timecard records for the logged-in user
$sql = "SELECT leave_id, leave_type, leave_start_date, leave_end_date, no_of_days, leave_reason, leave_doc, leave_req_status
        FROM leave_request 
        WHERE user_id = ? 
        ORDER BY leave_id DESC"; // Sort by latest records

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>