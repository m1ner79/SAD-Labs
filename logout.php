<?php
// Start session
session_start();

include("config.php");

// Get the username and sessionID from the session
$username = $_SESSION['username'];
$sessionID = session_id();

// Update logout time in eventslog table for the specific session
$query = "UPDATE eventslog SET logout_time=? WHERE username=? AND sessionID=?";
$logout_time = date('Y-m-d H:i:s', time());
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sss", $logout_time, $username, $sessionID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


// Clear session variables
session_unset();

// Destroy the session
session_destroy();

// Expire the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Rregenerate a new session ID
session_regenerate_id();

// Close database connection
mysqli_close($conn);

// Redirect to login page
header("Location: login.php");
exit();

?>