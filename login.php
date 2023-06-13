<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prevent the session cookie being accessed on the client with JavaScript
ini_set('session.cookie_httponly', 1);

// Ensuring session ID cannot be passed through URL
ini_set('session.use_only_cookies', 1);

// Set session timeout to 1 hour
ini_set('session.gc_maxlifetime', '3600'); // 1 hour

// Set session inactivity timeout to 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour

include('config.php');
// Start session
session_start();

// Destroy the session
session_destroy();

// Start session
session_start();

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

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["username"];
    $password = $_POST["password"];

    function sanitise_input($input)
    {
        $output = str_replace('&', '&amp;', $input);
        $output = str_replace('<', '&lt;', $output);
        $output = str_replace('>', '&gt;', $output);
        $output = str_replace('"', '&quot;', $output);
        $output = str_replace("'", '&#x27;', $output);
        $output = str_replace('/', '&#x2F;', $output);
        $output = preg_replace('/on\w+=/', '', $output); // remove any attribute that starts with "on"
        return $output;
    }

    // Sanitise the username and password
    $sanitised_username = sanitise_input($username);
    $sanitised_password = sanitise_input($password);

    include 'login_auth.php';
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
  <title>Login</title>
</head>

<body>
  <div class="d-flex align-items-center justify-content-center pt-3">
    <div class="card text-center">
      <div class="card-header">
        <h1>Login</h1>
      </div>
      <div class="card-body">
          <?php if (isset($_SESSION['error'])) : ?>
              <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['error']; ?>
              </div>
              <?php unset($_SESSION['error']); // remove error message from session variable ?>
          <?php endif; ?>
<!--          <div id="error-message" class="alert alert-danger d-none"></div>-->
        <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" id="username">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password">
          </div>
          <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="card-footer text-muted">
        Are you not registered? <a href="register.php"><b>Register</b></a>
      </div>
    </div>
  </div>
</body>

</html>