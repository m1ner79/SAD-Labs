<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('config.php');
include('func.php');

// Get form data
$sanitised_username = mysqli_real_escape_string($conn, $_POST["username"]);
$sanitised_password = mysqli_real_escape_string($conn, $_POST["password"]);

// Prepare and execute the query
$query = "SELECT salt, password, role FROM users WHERE username=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $sanitised_username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 1) {
    mysqli_stmt_bind_result($stmt, $salt, $stored_password, $role);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (verifyPassword($sanitised_password, $salt, $stored_password)) {

         // Check if the user is locked out
        $query = "SELECT login_attempt FROM eventslog WHERE username=? AND success=0 AND attempts = 5 ORDER BY login_attempt DESC LIMIT 1";
        $stmt_lockout = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt_lockout, "s", $sanitised_username);
        mysqli_stmt_execute($stmt_lockout);
        mysqli_stmt_bind_result($stmt_lockout, $lockout_timestamp);
        $is_locked_out = mysqli_stmt_fetch($stmt_lockout);
        mysqli_stmt_close($stmt_lockout); // Close the statement

        $time_elapsed = time() - strtotime($lockout_timestamp);
        $lockout_duration = 180;

        if ($is_locked_out && $time_elapsed < $lockout_duration) {
        // The user is locked out, redirect to the login page
        $_SESSION['error'] = 'The username ' . escape_angle_brackets($sanitised_username) . ' is locked out. Please wait ' . ($lockout_duration - $time_elapsed) . ' seconds before trying again.';
        header("Location: login.php");
        exit;
    }

        // Record successful login in eventslog table
        $login_time = date('Y-m-d H:i:s', time());
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO eventslog (username, login_time, logout_time, ip_address, success) VALUES (?, ?, NULL, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $sanitised_username, $login_time, $ip_address);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Generate a new session ID
        session_regenerate_id();
        $new_session = session_id();

        $_SESSION['username'] = $sanitised_username;
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['role'] = $role;
        $_SESSION['welcome_message'] = "Welcome " . escape_angle_brackets($sanitised_username);

        // Update the eventslog with attempts and session information
        $query = "UPDATE eventslog SET attempts=?, sessionID=?, last_activity=? WHERE username=? AND success=1 AND login_time=(SELECT MAX(login_time) FROM eventslog WHERE username=? AND success=1)";
        $stmt = mysqli_prepare($conn, $query);
        $last_activity = date('Y-m-d H:i:s', time());
        $attempts = 0;
        mysqli_stmt_bind_param($stmt, "issss", $attempts, $new_session, $last_activity, $sanitised_username, $sanitised_username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        // Redirect the user to the welcome page
        header('Location: home.php');
        exit;
    } else {
            // Get the last attempt information for the current user
            $query = "SELECT attempts FROM eventslog WHERE username=? ORDER BY id DESC LIMIT 1";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $sanitised_username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $last_attempts);
            $has_last_attempt = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Set the attempts count based on the last attempt
            if ($has_last_attempt) {
                $attempts = $last_attempts + 1;
            } else {
                $attempts = 1;
            }
            
            // Log the failed attempt in the database
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $login_attempt = date('Y-m-d H:i:s', time());
            $query = "INSERT INTO eventslog (username, attempts, login_attempt, ip_address, success) VALUES (?, ?, ?, ?, 0)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "siss", $sanitised_username, $attempts, $login_attempt, $ip_address);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

             // Check if the user is locked out
            if ($attempts >= 5) {
                $query = "SELECT login_attempt FROM eventslog WHERE username=? ORDER BY login_attempt DESC LIMIT 1";
                $stmt_lockout = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt_lockout, "s", $sanitised_username);
                mysqli_stmt_execute($stmt_lockout);
                mysqli_stmt_bind_result($stmt_lockout, $lockout_timestamp);
                $is_locked_out = mysqli_stmt_fetch($stmt_lockout);
                mysqli_stmt_close($stmt_lockout); // Close the statement

                if ($is_locked_out && (time() - strtotime($lockout_timestamp)) < 180) {
                    // The user is locked out, redirect to the login page
                    $_SESSION['error'] = 'The username ' . escape_angle_brackets($sanitised_username) . ' is locked out. Please wait ' . (180 - (time() - strtotime($lockout_timestamp))) . ' seconds before trying again.';
                    header("Location: login.php");
                    exit;
                }
            } 
            // Redirect the user to the login page
            $_SESSION['error'] = 'The username ' . escape_angle_brackets($sanitised_username) . ' and password could not be authenticated at the moment.';
            header("Location: login.php");
            exit;
        }
} else {
    // Log the failed attempt in the database
    $query = "INSERT INTO eventslog (username, attempts, login_attempt , ip_address) VALUES (?, '1', ?, ?)";
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $login_attempt = date('Y-m-d H:i:s', time());
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $sanitised_username, $login_attempt, $ip_address);
    mysqli_stmt_execute($stmt);
    echo $query;

    // Redirect the user to the login page
    $_SESSION['error'] = 'The username ' . escape_angle_brackets($sanitised_username) . ' and password could not be authenticated at the moment.';
    header("Location: login.php");
    exit;
}

// Lock the user out for 10 minutes if they have been inactive for 1 hour
$query = "SELECT * FROM eventslog WHERE username=? AND login_time + INTERVAL 1 HOUR < ? AND attempts < 5";
$now = date('Y-m-d H:i:s', time());
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $sanitised_username, $now);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_num_rows($stmt) == 1) {
    $query = "UPDATE eventslog SET logout_time=? + INTERVAL 1 MINUTE WHERE username=?";
    $now = date('Y-m-d H:i:s', time());
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $sanitised_username, $now);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect the user to the login page
    header('Location: login.php');
    exit;
}

// Log the user out after 1 hour
$query = "SELECT * FROM eventslog WHERE username=? AND login_time + INTERVAL 1 HOUR < ?";
$now = date('Y-m-d H:i:s', time());
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $sanitised_username, $now);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_num_rows($stmt) == 1) {
// Log the user out
    session_destroy();
    echo "<script>alert('Hey, " . strtoupper($_SESSION['username']) . "You have reached 1 hour limit.Please login again.'); window.location.replace('login.php');</script>";
//    header('Location: login.php');
    exit;
}
?>