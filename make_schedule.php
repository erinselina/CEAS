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
    <title>Schedule Arrangement</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/make_schedule.css">
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
                    <h2>Work Schedule</h2>
                    <button class="create_schedule_btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> Create Schedule</button><br><br>
                    <p class="loading">Loading schedule...</p>
                    <h1 style="margin-bottom: 0;" id="month-year" class="month-year"></h1>
                    <p style="text-align: center;"><span class="weekDisplay" id="weekDisplay"></p></span>

                    <!--Button For Filtering Work Schedule-->
                    <button class="prevWeekBtn" id="prevWeekBtn">← Previous Week</button>
                    <button class="nextWeekBtn" id="nextWeekBtn">Next Week →</button><br><br>

                    <!-- SCHEDULE TABLE -->
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Employee</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Monday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Tuesday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Wednesday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Thursday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Friday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Saturday</th>
                                <th style="background-color: #2c3e50; color: #ecf0f1;">Sunday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JavaScript will insert rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal To Create Schedule-->
    <div id="schedule_modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Assign Schedule</h3>
            <form class="schedule_form" id="schedule_form" action="process_schedule.php" method="POST">
                <table style="width: 100%;">
                    <tr><!-- Employee Selection -->
                        <th style="text-align: left"><label for="employee">Name:</label></th>
                        <th style="padding: 10px">
                            <select id="employee" name="employee" style="width: 100%;" required>
                                <?php
                                // Include database connection
                                include 'db.php';

                                // Query to get positions from the database
                                $sql = "SELECT id, first_name, last_name FROM users";
                                $result = $conn->query($sql);
                                // Loop through each row from the database
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</option>";
                                }
                                ?>

                            </select>
                        </th>
                    </tr>
                    <tr><!-- Date Picker -->
                        <th style="text-align: left"><label for="schedule_date">Date:</label></th>
                        <th style="padding: 10px"><input type="date" id="schedule_date" name="schedule_date" required></th>
                    </tr>
                    <tr><!-- Time In -->
                        <th style="text-align: left"><label for="schedule_time_in">Time In:</label></th>
                        <th style="padding: 10px"><input type="time" id="schedule_time_in" name="schedule_time_in" required></th>
                    </tr>
                    <tr><!-- Time Out -->
                        <th style="text-align: left"><label for="schedule_time_out">Time Out:</label></th>
                        <th style="padding: 10px"> <input type="time" id="schedule_time_out" name="schedule_time_out" required></th>
                    </tr>
                </table><br>
                <button class="submit-btn" id="submit-btn" type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Open the schedule_modal modal
        function openModal() {
            document.getElementById("schedule_modal").style.display = "flex";
        }

        // Close the schedule_modal modal
        function closeModal() {
            document.getElementById("schedule_modal").style.display = "none";
        }

        document.addEventListener("DOMContentLoaded", function() {
            let currentDate = new Date(); // Track the current displayed week
            fetchSchedule(currentDate);

            // Event Listeners for Week Navigation
            document.getElementById("prevWeekBtn").addEventListener("click", function() {
                currentDate.setDate(currentDate.getDate() - 7);
                fetchSchedule(currentDate);
            });

            document.getElementById("nextWeekBtn").addEventListener("click", function() {
                currentDate.setDate(currentDate.getDate() + 7);
                fetchSchedule(currentDate);
            });

            function fetchSchedule(date) {
                const loadingText = document.querySelector(".loading");
                const tableBody = document.querySelector(".schedule-table tbody");
                const tableHeadRow = document.querySelector(".schedule-table thead tr");

                // Get Monday of the selected week
                let startOfWeek = new Date(date);
                startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay() + 1);
                let formattedWeekStart = startOfWeek.toISOString().split("T")[0];

                // Update displayed month & year
                document.getElementById("month-year").innerText = startOfWeek.toLocaleString("default", {
                    month: "long",
                    year: "numeric"
                });

                // Update week display
                document.getElementById("weekDisplay").innerText = `Week of ${startOfWeek.toLocaleDateString()}`;

                fetch(`fetch_schedule.php?week_start=${formattedWeekStart}`)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = ""; // Clear previous data
                        tableHeadRow.innerHTML = "";

                        // Hide loading text
                        loadingText.style.display = "none";

                        if (Object.keys(data).length === 0) {
                            loadingText.innerText = "No schedule available.";
                            loadingText.style.display = "block";
                            return;
                        }

                        // Add "Employee" column header
                        let employeeTh = document.createElement("th");
                        employeeTh.innerText = "Employee";
                        employeeTh.style.backgroundColor = "#2c3e50"; // Apply style dynamically
                        employeeTh.style.color = "white";
                        employeeTh.style.padding = "10px";
                        employeeTh.style.textAlign = "left";
                        tableHeadRow.appendChild(employeeTh);

                        // Generate headers with weekday and date
                        const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                        days.forEach((day, index) => {
                            let date = new Date(startOfWeek);
                            date.setDate(date.getDate() + index);
                            let formattedDate = date.getDate(); // Get day number

                            let th = document.createElement("th");
                            th.style.backgroundColor = "#2c3e50"; // Apply style dynamically
                            th.style.color = "white";
                            th.style.padding = "10px";
                            tableHeadRow.appendChild(th);
                            th.innerHTML = `${day} - ${formattedDate}`;
                        });

                        // Populate table with schedule data
                        for (let employee in data) {
                            const row = document.createElement("tr");

                            // Employee Name
                            const nameCell = document.createElement("td");
                            nameCell.innerHTML = `<strong>${employee}</strong>`;
                            nameCell.style.textAlign = "left";
                            row.appendChild(nameCell);

                            // Fill schedule data
                            days.forEach(day => {
                                const cell = document.createElement("td");
                                const shiftValue = data[employee][day] || "OFF";
                                cell.innerText = shiftValue;


                                // Check for "ABSENT" and apply red style
                                if (shiftValue === "ABSENT") {
                                    cell.style.color = "red";
                                    cell.style.fontWeight = "bold";
                                } else if (shiftValue !== "OFF") {
                                    cell.classList.add("shift-cell");
                                    cell.dataset.userId = data[employee]['user_id'];
                                    cell.dataset.date = data[employee][`${day}_date`];
                                    cell.dataset.time_in = data[employee][`${day}_time_in`] || "00:00:00";
                                    cell.dataset.time_out = data[employee][`${day}_time_out`] || "00:00:00";
                                    cell.dataset.shift = shiftValue;

                                    cell.addEventListener("click", function() {
                                        const userId = this.dataset.userId;
                                        const date = this.dataset.date;
                                        const time_in = this.dataset.time_in;
                                        const time_out = this.dataset.time_out;

                                        if (confirm(`Mark employee as Absent on ${date}?`)) {
                                            fetch('mark_absent.php', {
                                                    method: 'POST',
                                                    headers: {
                                                        "Content-Type": "application/x-www-form-urlencoded"
                                                    },
                                                    body: `user_id=${userId}&date=${date}&time_in=${time_in}&time_out=${time_out}`
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    alert(data.message);
                                                    this.innerText = "ABSENT";
                                                    this.style.color = "red";
                                                    this.style.fontWeight = "bold";
                                                })
                                                .catch(err => alert("Failed to update absence."));
                                        }
                                    });
                                }

                                row.appendChild(cell);
                            });

                            tableBody.appendChild(row);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching schedule:", error);
                        loadingText.innerText = "Failed to load schedule.";
                    });
            }
        });
    </script>

</body>

</html>