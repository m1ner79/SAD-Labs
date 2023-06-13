<?php
$host = "localhost";
$username = "SADUSER";
$password = "SADUSER";
$dbname = "sad_project";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connect Error: ' . $conn->connect_error);
}
?>