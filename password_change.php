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

// Get the username and sessionID from the session
$username = $_SESSION['username'];
$sessionID = session_id();

// Check if user is authenticated
if (!$_SESSION["authenticated"]) {
    header("Location: login.php");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["submit"])) {
    // Check if the page was opened in a new tab
    if (!empty($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) !== $_SERVER['HTTP_HOST']) {
        $_SESSION['error'] = "You are not allowed to perform this action in a new tab.";
        // Clear session variables
        session_unset();

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: login.php");
        exit();
    }
    
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'CSRF token is invalid. Please try again.';
        // Clear session variables
        session_unset();

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: login.php");
        exit();
    } else {
        $old_password = $_GET["old_password"];
        $new_password = $_GET["new_password"];
        $confirm_new_password = $_GET["confirm_new_password"];

        if ($new_password !== $confirm_new_password) {
            $_SESSION['error'] = "New passwords do not match.";
        } else {
            $username = $_SESSION["username"];
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (verifyPassword($old_password, $user["salt"], $user["password"])) {
                $new_hash_salt = create_hash($new_password);
                $new_hash = $new_hash_salt["hash"];
                $new_salt = $new_hash_salt["salt"];

                $query = "UPDATE users SET password = ?, salt = ? WHERE username = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sss", $new_hash, $new_salt, $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Update logout time  and password_change event in eventslog table for the specific session
                $query = "UPDATE eventslog SET logout_time=?, password_change=1 WHERE username=? AND sessionID=?";
                $logout_time = date('Y-m-d H:i:s', time());
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sss", $logout_time, $username, $sessionID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Clear session variables
                session_unset();

                // Destroy the session
                session_destroy();

                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "Old password is incorrect.";
            }
        }
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <title>Password Change</title>
    </head>

    <body>
        <div class="d-flex align-items-center justify-content-center pt-3">
            <div class="card text-center">
                <div class="card-header">
                    <h1>Change Password</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_SESSION['error']; ?>
                        </div>
                        <?php unset($_SESSION['error']); // remove error message from session variable 
                        ?>
                    <?php endif; ?>
                    <form action="password_change.php" method="get" enctype='multipart/form-data'>
                        <div class="form-group">
                            <input type="hidden" id="username" value="<?php echo htmlspecialchars($username); ?>">
                            <!-- Adding the hidden input field for the CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <label for="old_password">Old password:</label>
                            <input type="password" name="old_password" class="form-control" id="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New password:</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" required>
                            <ul id="passwordErrors" class="text-danger"></ul>
                        </div>
                        <div class="form-group">
                            <label for="confirm_new_password">Confirm new password:</label>
                            <input type="password" name="confirm_new_password" class="form-control" id="confirm_new_password" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <b>Back to <a href="home.php">Home</a></b>
                </div>
            </div>
        </div>
        <script>
        function isValidPassword(password) {
            const errors = [];

            if (password.length < 8) {
                errors.push("Password must be at least 8 characters long.");
            }
            if (!/[a-z]/.test(password)) {
                errors.push("Password must have at least one lowercase letter.");
            }
            if (!/[A-Z]/.test(password)) {
                errors.push("Password must have at least one uppercase letter.");
            }
            if (!/[0-9]/.test(password)) {
                errors.push("Password must have at least one number.");
            }
            if (!/[!@#$%^&*()]/.test(password)) {
                errors.push("Password must have at least one special character.");
            }

            return { valid: errors.length === 0, errors };
        }

        function checkPassword(event) {
            const passwordInput = event.target;
            const form = passwordInput.closest('form');
            const passwordErrors = form.querySelector("#passwordErrors");
            const password = passwordInput.value;

            const validationResult = isValidPassword(password);
            passwordErrors.innerHTML = "";

            for (const error of validationResult.errors) {
                const errorElement = document.createElement("li");
                errorElement.textContent = error;
                passwordErrors.appendChild(errorElement);
            }

            const submitButton = form.querySelector("[type='submit']");
            submitButton.disabled = !validationResult.valid;
        }

        function addPasswordInputEventListener() {
            const newPasswordInput = document.getElementById("new_password");
            if (newPasswordInput) {
                newPasswordInput.addEventListener("input", checkPassword);
            }
        }

        document.addEventListener("DOMContentLoaded", addPasswordInputEventListener);

        const observer = new MutationObserver((mutationsList, observer) => {
            for (const mutation of mutationsList)
            {
                if (mutation.type === 'childList') {
                addPasswordInputEventListener();
                }
            }
        });

        observer.observe(document.body, { attributes: false, childList: true, subtree: true });
        </script>
    </body>
    </html>