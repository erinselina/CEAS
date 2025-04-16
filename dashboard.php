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
    <title>Dashboard</title>
    <link rel="icon" href="images/CEAS_logo.png">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div id="dashboard_main_container">
        <div class="dashboard_sidebar">
            <!-- Include the Sidebar -->
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
                    <h2>Dashboard</h2>
                    <div class="graph1">
                        <h2 style="text-align: center;">Total Absences by Employee</h2>
                        <label for="monthFilter">Filter by Month:</label>
                        <select id="monthFilter">
                            <option value="">-- All Months --</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <!-- Add other months as needed -->
                        </select>
                        <canvas id="absenceChart"></canvas><br><br>
                    </div>
                    <script>
                        let absenceChart; // to store chart instance

                        function loadAbsenceChart(month = '') {
                            fetch(`attendance_data.php?month=${month}`)
                                .then(response => response.json())
                                .then(data => {
                                    const names = data.map(row => row.name);
                                    const totals = data.map(row => parseInt(row.total_absences));

                                    const ctx = document.getElementById('absenceChart').getContext('2d');

                                    // Destroy existing chart if it exists
                                    if (absenceChart) {
                                        absenceChart.destroy();
                                    }

                                    absenceChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: names,
                                            datasets: [{
                                                label: 'Total Absences',
                                                data: totals,
                                                backgroundColor: 'rgba(255, 99, 132, 0.7)'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert('Error loading data!');
                                });
                        }

                        // Load default chart
                        loadAbsenceChart();

                        // Listen for dropdown change
                        document.getElementById('monthFilter').addEventListener('change', function() {
                            const selectedMonth = this.value;
                            loadAbsenceChart(selectedMonth);
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</body>

</html>