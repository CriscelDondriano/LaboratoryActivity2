<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave this empty if you haven't set a password for MySQL
$dbname = "database1"; // Replace 'db' with the name of your database
$port = 3306; // The default MySQL port is 3306

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
