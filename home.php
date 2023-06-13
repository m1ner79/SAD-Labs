<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set session timeout
ini_set('session.gc_maxlifetime', 600);

// Set session inactivity timeout to 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour
// Start session
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

// Check if user is authenticated
if (!$_SESSION["authenticated"]) {
    header("Location: login.php");
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
  <title>Home</title>
</head>

<body>
  <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand"><b>SAD 2023</b></a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="cookie.php">All About Cookies! by Fel</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="michal.php">Michal's page</a>
          </li>
        </ul>
      </div>
        <?php if (strtoupper($_SESSION["username"]) === "ADMIN") : ?>
            <div>
                <a class="nav-link" href="eventslog.php">Event Log</a>
            </div>
        <?php endif; ?>
        <div>
        <a class="nav-link" href="password_change.php">Password Change</a>
      </div>
      <div>
        <a class="nav-link disabled"><b><?php echo strtoupper($_SESSION["username"]); ?></b></a>
      </div>
      <form class="d-flex" action="logout.php" method="post">
        <button type="submit" class="btn btn-light">Logout</button>
      </form>
    </div>
    </div>
  </nav>
  <div class="container">
    <div class="jumbotron text-center">
        <div id="welcome-banner">
            <h1>Welcome <b><?php echo strtoupper(escape_angle_brackets($_SESSION["username"])); ?></b>!</h1>
            <p>Thank you for visiting our website.</p>
        </div>

        <script type="text/javascript">
            setTimeout(function() {
                document.getElementById('welcome-banner').style.display = 'none';
            }, 5000);
        </script>
      <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active" data-bs-interval="10000">
            <img src="https://excess-xss.com/reflected-xss.png" class="d-block w-100" alt="...">
            <!-- <div class="carousel-caption d-none d-md-block">
              <h5>Reflected XSS</h5>
              <p>Reflected XSS, where the malicious string originates from the victim's request.</p>
            </div> -->
          </div>
          <div class="carousel-item" data-bs-interval="2000">
            <img src="https://excess-xss.com/dom-based-xss.png" class="d-block w-100" alt="...">
            <!-- <div class="carousel-caption d-none d-md-block">
              <h5>DOM-based XSS</h5>
              <p>DOM-based XSS, where the vulnerability is in the client-side code rather than the server-side code.</p>
            </div> -->
          </div>
          <div class="carousel-item">
            <img src="https://media.geeksforgeeks.org/wp-content/cdn-uploads/20190516153259/StoredXSS.png" class="d-block w-100" alt="...">
            <!-- <div class="carousel-caption d-none d-md-block">
              <h5>Persistent (Stored) XSS</h5>
              <p>Persistent (Stored) XSS, where the malicious string originates from the website's database.</p>
            </div> -->
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js"></script>

</body>

</html>