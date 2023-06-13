<?php
session_start();
include("config.php");
include("func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Concatenate salt with password and hash the combined value
    $hashedPassword = create_hash($password);

    if (!empty($username)) {
        // Insert the user info into the database
        $sql = "INSERT INTO users (username, salt, password, role) VALUES (?, ?, ?, 'normal');";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword['salt'], $hashedPassword['hash']);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php");
            die;
        }
    } else {
        echo '<h3 class="d-flex alert alert-danger justify-content-center" role="alert">Please enter username and password.</h3>';
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
    <link rel="stylesheet" href="style.css" />
    <title>Registration</title>
</head>

<body>
    <div class="d-flex align-items-center justify-content-center pt-3">
        <div class="card text-center">
            <div class="card-header">
                <h1>Register</h1>
            </div>
            <div class="card-body">
                <form method="post" enctype='multipart/form-data'>
                    <div class="form-group">
                        <label for="input" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <ul id="passwordErrors" class="text-danger"></ul>
                    </div>
                    <button type="submit" class="btn btn-primary" name="new_post">Submit</button>
                </form>
                <div class="card-footer text-muted">
                    Are you already registered? <a href="login.php"><b>Login</b></a>
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
            const newPasswordInput = document.getElementById("password");
            if (newPasswordInput) {
                newPasswordInput.addEventListener("input", checkPassword);
            }
        }

        document.addEventListener("DOMContentLoaded", addPasswordInputEventListener);

        const observer = new MutationObserver((mutationsList, observer) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    addPasswordInputEventListener();
                }
            }
        });

        observer.observe(document.body, { attributes: false, childList: true, subtree: true });
        </script>
</body>
</html>