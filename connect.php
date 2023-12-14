<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "total gas";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;

?>