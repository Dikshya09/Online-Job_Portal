<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jobportal";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $Connection->connect_error);
}
?>
