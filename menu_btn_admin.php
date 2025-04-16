<div class="dashboard_content">
    <div class="dashboard_content_main">
        <?php
        $sql = "SELECT roles.role_name 
                FROM users 
                JOIN roles ON users.position = roles.id 
                WHERE users.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result(); // ✅ Ensures the result is buffered
        $stmt->bind_result($position);
        $stmt->fetch();
        $stmt->close();
        ?>

        <button class="btn_punch" onclick="window.location.href='clock_in_out.php'"><i style="font-size: 20px;" class="fa fa-clock"></i><br><br>Clock-in & Clock-out</button>
        <button class="btn_punch" onclick="window.location.href='make_schedule.php'"><i style="font-size: 20px;" class="fa fa-calendar"></i><br><br>Make Schedule</button>
        <button class="btn_punch" onclick="window.location.href='view_LR.php'"><i style="font-size: 20px;" class="fa-solid fa-pen"></i><br><br>Apply for Leave</button>
        <button class="btn_punch" onclick="window.location.href='profile.php'"><i style="font-size: 20px;" class="fa-solid fa-circle-user"></i><br><br>Profile Setting</button><br><br>

        <button class="btn_punch" onclick="window.location.href='view_employee.php'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>List of Employees</button>
        <button class="btn_punch" onclick="window.location.href='view_emps_attendances.php'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Time Cards</button>
        <button class="btn_punch" onclick="window.location.href='view_emps_absences.php'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Absent List</button>
        <button class="btn_punch" onclick="window.location.href='view_emps_LR.php'"><i style="font-size: 20px;" class="fa-solid fa-list"></i><br><br>Employees Leave Requests</button>
    </div>
</div>