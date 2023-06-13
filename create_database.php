<?php
include("config.php");
include("func.php");

// Create the "users" table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    salt VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'normal') NOT NULL
)";

$sql2 = "CREATE TABLE IF NOT EXISTS eventslog (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    attempts INT(11) NOT NULL DEFAULT '0',
    password_change INT(11) NOT NULL DEFAULT '0',
    login_attempt DATETIME DEFAULT NULL,
    login_time DATETIME DEFAULT NULL,
    logout_time DATETIME DEFAULT NULL,
    ip_address VARCHAR(255) DEFAULT NULL,
    sessionID VARCHAR(255) DEFAULT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) NOT NULL DEFAULT '0'
)";

mysqli_query($conn, $sql2);

if (mysqli_query($conn, $sql)) {
    // Generate the salt and hashed password for ADMIN
    $password = "SaD_2023!";
    $hashed_password = create_hash($password);
    // Insert ADMIN into the "users" table
    $username = "ADMIN";
    $role = "admin";
    $sql2 = "INSERT IGNORE INTO users (username, salt, password, role) VALUES ('$username', '$hashed_password[salt]', '$hashed_password[hash]', '$role')";
    if (mysqli_query($conn, $sql2)) {
        echo "Second user inserted successfully!";
    } else {
        echo "Error inserting second user into the table: " . mysqli_error($conn);
    }
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

$conn->close();

// Redirect to login page after database creation
header("Location: login.php");
exit;
