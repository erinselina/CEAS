<?php
$servername = "localhost";
$username = "root"; // database username
$password = "";
$dbname = "ceas"; // database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to UTF-8 (optional but recommended)
$conn->set_charset("utf8");

?>
