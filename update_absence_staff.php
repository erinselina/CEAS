<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id']; // Get logged-in user ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; // Absence record ID
    $date = $_POST['date'];
    $schedule_time_in = $_POST['schedule_time_in'];
    $schedule_time_out = $_POST['schedule_time_out'];
    $reason = $_POST['reason'];

    $support_doc = NULL; // Default value

    // Handle file upload
    if (isset($_FILES["support_doc"]) && $_FILES["support_doc"]["error"] == 0) {
        $upload_dir = "absent_attachment/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["support_doc"]["name"]);
        $target_file = $upload_dir . $file_name;

        $allowed_types = ['pdf', 'jpg', 'png'];
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["support_doc"]["tmp_name"], $target_file)) {
                $support_doc = $target_file; // Store path to DB
            } else {
                die("Error: Failed to upload file.");
            }
        } else {
            die("Error: Invalid file type. Only JPG, PNG, and PDF allowed.");
        }
    }

    // If no file uploaded, retain the existing path
    if ($support_doc === NULL) {
        $getExisting = $conn->prepare("SELECT support_doc FROM absences WHERE id = ?");
        $getExisting->bind_param("i", $id);
        $getExisting->execute();
        $getExisting->bind_result($existing_doc);
        $getExisting->fetch();
        $getExisting->close();

        $support_doc = $existing_doc;
    }

    // Update into database
    $sql = "UPDATE absences 
            SET date = ?, schedule_time_in = ?, schedule_time_out = ?, reason = ?, support_doc = ?, status = 'Pending' 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $date, $schedule_time_in, $schedule_time_out, $reason, $support_doc, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Absent record updated'); window.location.href='view_absent.php';</script>";
    } else {
        echo "<script>alert('Error updating absent record'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
