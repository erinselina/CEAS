<?php
include 'db.php';

$week_start = isset($_GET['week_start']) ? $_GET['week_start'] : date('Y-m-d');
$week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));

$sql = "SELECT users.id AS user_id, users.first_name, users.last_name, schedule.schedule_date, schedule.schedule_time_in, schedule.schedule_time_out 
        FROM schedule 
        JOIN users ON schedule.user_id = users.id 
        WHERE schedule.schedule_date BETWEEN ? AND ?
        ORDER BY users.first_name, schedule.schedule_date";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $week_start, $week_end);
$stmt->execute();
$result = $stmt->get_result();

$schedule_data = [];
$days_map = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

while ($row = $result->fetch_assoc()) {
    $employee = $row['first_name'] . " " . $row['last_name'];
    $user_id = $row['user_id'];
    $date = $row['schedule_date'];
    $day_name = date('l', strtotime($date));
    $time_in = date("g:i A", strtotime($row['schedule_time_in']));
    $time_out = date("g:i A", strtotime($row['schedule_time_out']));

    $shift = ($row['schedule_time_in'] && $row['schedule_time_out'])
        ? "$time_in - $time_out"
        : "ABSENT";

    // Initialize if first time
    if (!isset($schedule_data[$employee])) {
        $schedule_data[$employee] = ['user_id' => $user_id];
    }

    $schedule_data[$employee][$day_name] = $shift;
    $schedule_data[$employee][$day_name . '_date'] = $date;

    $schedule_data[$employee][$day_name . '_time_in'] = $row['schedule_time_in'];
    $schedule_data[$employee][$day_name . '_time_out'] = $row['schedule_time_out'];
}

echo json_encode($schedule_data);
$conn->close();
