<?php
require_once('connect.php');

// SQL query to create the daily_treasury table
$sql = "CREATE TABLE setting (
    id INT(20) AUTO_INCREMENT PRIMARY KEY,
    option  VARCHAR(150),
    sys_type  VARCHAR(150),
    value  VARCHAR(250)
)";

// Execute the query and handle errors
if ($conn->query($sql) === TRUE) {
    echo "Table number created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
