<?php
// Connect as root
$host = "localhost";
$username = "root";
$password = ""; // Set your root password if required
$root_conn = new mysqli($host, $username, $password);

// Check connection
if ($root_conn->connect_error) {
    die('Connect Error: ' . $root_conn->connect_error);
}

// Create user SADUSER
$sql = "CREATE USER 'SADUSER'@'localhost' IDENTIFIED BY 'SADUSER'";
if ($root_conn->query($sql) === TRUE) {
    echo "User SADUSER created successfully<br>";
} else {
    echo "Error creating user SADUSER: " . $root_conn->error . "<br>";
}

// Create the database
$dbname = "sad_project";
$sql = "CREATE DATABASE " . $dbname;
if ($root_conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $root_conn->error . "<br>";
}

// Grant privileges to SADUSER
$sql = "GRANT ALL PRIVILEGES ON " . $dbname . ".* TO 'SADUSER'@'localhost'";
if ($root_conn->query($sql) === TRUE) {
    echo "Privileges granted to SADUSER successfully<br>";
} else {
    echo "Error granting privileges: " . $root_conn->error . "<br>";
}

// Close the connection
$root_conn->close();

// Redirect to create_database page after SADUSER creation
header("Location: create_database.php");
?>