<?php
session_start();
include("config.php");
include("func.php");

// Check if the session has a last activity time
if (isset($_SESSION['last_activity'])) {
    // Check if the idle time has been exceeded
    if (time() - $_SESSION['last_activity'] > 600) {
        // Timeout exceeded, destroy the session and redirect to logout page
        session_destroy();
        header("Location: logout.php");
        exit;
    }
}

// Update the last activity time
$_SESSION['last_activity'] = time();

// Check if the user is authenticated and if they are an admin
if (!$_SESSION["authenticated"] || strtoupper($_SESSION["username"]) !== "ADMIN") {
    header("Location: login.php");
    exit;
}

// Fetch data from the eventslog table
$query = "SELECT * FROM eventslog";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css" />
    <title>Events Log</title>
</head>

<body>
    <div class="container">
        <div class="d-flex align-items-center justify-content-center pt-3">
            <div class="card text-center">
                <div class="card-header">
                    <h1>Events Log</h1>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Failed Attempts</th>
                                <th>Password Change</th>
                                <th>Login Attempt</th>
                                <th>Session ID</th>
                                <th>IP Address</th>
                                <th>Login Time</th>
                                <th>Last Activity</th>
                                <th>Logout Time</th>
                                <th>Success</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo escape_angle_brackets($row['username']); ?></td>
                                    <td><?php echo $row['attempts']; ?></td>
                                    <td><?php echo $row['password_change'] ? "Yes" : "No"; ?></td>
                                    <td><?php echo $row['login_attempt']; ?></td>
                                    <td><?php echo $row['sessionID']; ?></td>
                                    <td><?php echo $row['ip_address']; ?></td>
                                    <td><?php echo $row['login_time']; ?></td>
                                    <td><?php echo $row['last_activity']; ?></td>
                                    <td><?php echo $row['logout_time']; ?></td>
                                    <td><?php echo $row['success'] ? "Yes" : "No"; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted">
                <b>Back to <a href="home.php">Home</a></b>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
