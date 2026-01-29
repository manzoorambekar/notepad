<?php

// Database configuration
$db_host = 'localhost';      // Database server address
$db_user = 'root';
$db_pass = '';
$db_name = 'notes_db';

// Create connection to MariaDB using MySQLi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error . "\n");
}

$conn->set_charset("utf8mb4");

echo "DB connection Successful!\n";
?>