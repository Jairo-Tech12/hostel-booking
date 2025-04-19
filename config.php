<?php
// config.php

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

// Create a new mysqli connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
