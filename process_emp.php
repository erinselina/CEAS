<?php
print_r($_POST);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone-number'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
    $position = $_POST['position'];

    // Check if profile picture is uploaded
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = "images/" . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    } else {
        // Set default profile picture if none uploaded
        $profile_picture = "images/default_pfp.jpg";
    }

    // Insert employee into `users` table
    $stmt = $conn->prepare("INSERT INTO users (profile_picture, first_name, last_name, email, phone_number, password, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $profile_picture, $first_name, $last_name, $email, $phone_number, $password, $position);
    $stmt->execute();
    $user_id = $stmt->insert_id; // Get new user ID

    // Display popup message and redirect back to the form
    echo "<script>
            alert('Employee saved successfully!');
            window.location.href = '../.vscode/root_home.php';
          </script>";
}
