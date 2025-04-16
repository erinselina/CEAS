<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="CSS/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <h3>Calculate Working Hours</h3>

    <form class="schedule_form" id="schedule_form" action="" method="POST">
        <table border="1">
            <tr>
                <th style="text-align: left"><label for="input_date">Date:</label></th>
                <th style="padding: 10px"><input type="date" id="input_date" name="input_date" required></th>
            </tr>
            <tr>
                <th style="text-align: left"><label for="time_in">Time In:</label></th>
                <th style="padding: 10px"><input type="time" id="time_in" name="time_in" required></th>
            </tr>
            <tr>
                <th style="text-align: left"><label for="time_out">Time Out:</label></th>
                <th style="padding: 10px"><input type="time" id="time_out" name="time_out" required></th>
            </tr>
        </table><br>
        <button class="submit-btn" id="submit-btn" type="submit" name="submit">Save</button>
    </form>
    <br>

    <?php
    if (isset($_POST['submit'])) {
        $date = $_POST['input_date'];
        $timeIn = $_POST['time_in'];
        $timeOut = $_POST['time_out'];
    
        // Convert to DateTime
        $start = DateTime::createFromFormat('H:i', $timeIn);
        $end = DateTime::createFromFormat('H:i', $timeOut);
    
        // Handle overnight shift
        if ($end < $start) {
            $end->modify('+1 day');
        }
    
        // Calculate interval in seconds
        $intervalSeconds = $end->getTimestamp() - $start->getTimestamp();
    
        // Convert to hours (as float)
        $totalHours = $intervalSeconds / 3600;
    
        // Format to 2 decimal places
        $totalHoursFormatted = number_format($totalHours, 2);
    
        // Display output
        echo "<p id='date'>Date: $date</p><br>";
        echo "<p id='timeIn'>Time In: $timeIn</p><br>";
        echo "<p id='timeOut'>Time Out: $timeOut</p><br>";
        echo "<p id='totalhrs'>Total Hours Worked: $totalHoursFormatted hours</p><br>";
    }
    
    ?>
</body>

</html>
