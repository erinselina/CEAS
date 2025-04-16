<div class="dashboard_sidebar">
    <div class="dashboard_sidebar_user">
        <h3 class="dashboard_logo">CEAS</h3>
        <img src="<?php echo $profile_picture; ?>" alt="User Image">

        <?php

        $sql = "SELECT roles.role_name 
                FROM users 
                JOIN roles ON users.position = roles.id 
                WHERE users.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($position);
        $stmt->fetch();
        $stmt->close();
        ?>
        
        <h3 style="margin-bottom: 0%; padding-bottom: 5px; font-size:20px;"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
        <p style="margin: 0%; font-style:italic;"><?php echo htmlspecialchars($position); ?></p><br>
    </div>

    <div class="dashboard_sidebar_menus">
        <a href="home.php"><i class="fa-solid fa-house-chimney"></i> Home</a>
        <a href="dashboard.php"><i class="fa fa-clock"></i> Dashboard</a>
        <a href="make_schedule.php"><i class="fa fa-calendar"></i> Schedule Shift</a>
        <a href="clock_in_out.php"><i class="fa fa-clock"></i> Clock-in/out</a>
        <a href="attendance_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Attendance History</a>
        <a href="view_absent.php"><i class="fa-solid fa-list"></i> View Absences</a>
        <a href="view_LR.php"><i class="fa-solid fa-pen"></i> Apply for Leaves</a>

        <div class="dropdown">
            <button class="dropbtn">Employee Information
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="view_employee.php"><i class="fa-solid fa-list"></i> List of Employees</a>
                <a href="view_emps_attendances.php"><i class="fa-solid fa-list"></i> Employees Time Cards</a>
                <a href="view_emps_LR.php"><i class="fa-solid fa-list"></i> Employees Leave Requests</a>
                <a href="view_emps_absences.php"><i class="fa-solid fa-list"></i> Employees Absences</a><br><br>
            </div>
        </div>

        <a href="profile.php"><i class="fa-solid fa-circle-user"></i> Profile Setting</a><br><br><br>
    </div>
</div>