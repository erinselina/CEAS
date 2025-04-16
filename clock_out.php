<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id']; // Retrieve logged-in user ID
    $tc_date = $_POST['tc_date']; // Get date from JavaScript
    $tc_timeOUT = $_POST['tc_timeOUT']; // Get time from JavaScript

    // Update clock-out time with modified condition
    $sqlUpdate = "UPDATE employee_timecard 
                  SET tc_timeOUT = ? 
                  WHERE user_id = ? AND tc_date = ? AND (tc_timeOUT IS NULL OR tc_timeOUT = '00:00:00')";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sis", $tc_timeOUT, $user_id, $tc_date);
    $stmtUpdate->execute();

    // Check if any row was affected (updated)
    if ($stmtUpdate->affected_rows > 0) {
        // Retrieve both time in and time out
        $sqlSelect = "SELECT tc_timeIN, tc_timeOUT FROM employee_timecard 
                      WHERE user_id = ? AND tc_date = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->bind_param("is", $user_id, $tc_date);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['tc_timeIN'] && $row['tc_timeOUT']) {
            // Log times to debug
            error_log("Time In: " . $row['tc_timeIN']);
            error_log("Time Out: " . $row['tc_timeOUT']);

            $start = DateTime::createFromFormat('H:i:s', $row['tc_timeIN']);
            $end = DateTime::createFromFormat('H:i:s', $row['tc_timeOUT']);

            // Handle overnight shift
            if ($end < $start) {
                $end->modify('+1 day');
            }

            // Calculate difference in seconds
            $diffInSeconds = $end->getTimestamp() - $start->getTimestamp();
            $totalHours = $diffInSeconds / 3600;
            $totalHoursFormatted = number_format($totalHours, 2);

            // Store total hours in the database
            $sqlTotal = "UPDATE employee_timecard 
                         SET tc_ttlHours = ? 
                         WHERE user_id = ? AND tc_date = ?";
            $stmtTotal = $conn->prepare($sqlTotal);
            $stmtTotal->bind_param("dis", $totalHoursFormatted, $user_id, $tc_date);
            $stmtTotal->execute();

            echo "Clock out recorded. Total hours worked: $totalHoursFormatted";
        } else {
            echo "Failed to retrieve time in/out.";
        }
    } else {
        echo "⚠️ No matching row found or already clocked out.";
    }
}
