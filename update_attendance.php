<?php
include 'db.php';

if (isset($_POST['tc_id'])) {
    $tc_id = $_POST['tc_id'];
    $tc_date = $_POST['schedule_date'];
    $tc_timeIN = $_POST['schedule_time_in'];
    $tc_timeOUT = $_POST['schedule_time_out'];

    // Calculate total hours only if time_in and time_out are provided
    if ($tc_timeIN && $tc_timeOUT) {
        // Create DateTime objects from time inputs
        $start = DateTime::createFromFormat('H:i:s', $tc_timeIN);
        $end = DateTime::createFromFormat('H:i:s', $tc_timeOUT);

        // Check if the time formats are valid
        if ($start === false || $end === false) {
            echo "<script>alert('Invalid time format. Please ensure time is in HH:MM:SS format.');</script>";
            exit;
        }

        // Handle overnight shift: if time_out is before time_in, add a day to time_out
        if ($end < $start) {
            $end->modify('+1 day');
        }

        // Calculate the difference in seconds
        $diffInSeconds = $end->getTimestamp() - $start->getTimestamp();
        $totalHours = $diffInSeconds / 3600;  // Convert to hours
        $totalHoursFormatted = number_format($totalHours, 2);  // Format to two decimal places

        // Log the calculated total hours for debugging purposes
        error_log("Calculated total hours: " . $totalHoursFormatted);

        // Update the total hours in the database
        $updateHoursQuery = "UPDATE employee_timecard SET tc_ttlHours = ? WHERE tc_id = ?";
        $stmtUpdateHours = $conn->prepare($updateHoursQuery);
        if ($stmtUpdateHours === false) {
            die("Prepare failed for updating total hours: " . $conn->error);
        }
        $stmtUpdateHours->bind_param("di", $totalHoursFormatted, $tc_id);
        $stmtUpdateHours->execute();
    }

    // Double-check these column names match your DB exactly for updating timecard record
    $query = "UPDATE employee_timecard SET tc_date = ?, tc_timeIN = ?, tc_timeOUT = ? WHERE tc_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed for updating timecard record: " . $conn->error);
    }

    $stmt->bind_param("sssi", $tc_date, $tc_timeIN, $tc_timeOUT, $tc_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Attendance details updated successfully.'); window.location.href='view_emps_attendances.php';</script>";
        } else {
            echo "<script>alert('No rows were updated. Check if the data actually changed or the record exists.');</script>";
        }
    } else {
        echo "<script>alert('Error updating attendance details: " . $stmt->error . "');</script>";
    }
}

$conn->close();
?>
