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
    <link rel="stylesheet" href="style.css">
    <title>Michal's Page</title>
</head>
<body>
<div class="d-flex align-items-center justify-content-center pt-3">
            <div class="card text-center">
                <div class="card-header">
                    <h1>Dad Jokes</h1>
                </div>
                <div class="card-body">
                    <p>Why don't scientists trust atoms? Because they make up everything.</p>
                    <p>Why did the coffee file a police report? It got mugged.</p>
                    <p>What's the best way to watch a fly fishing tournament? Live stream.</p>
                    <p>What do you call fake spaghetti? An impasta.</p>
                    <p>Why did the belt get arrested? It held up the pants.</p>
                    <p>How does a penguin build its house? Igloos it together.</p>
                    <p>Why don't skeletons fight each other? They don't have the guts.</p>
                    <p>What do you get when you cross a snowman and a shark? Frostbite.</p>
                    <p>How do you catch a squirrel? Climb up a tree and act like a nut.</p>
                    <p>Why did the man run around his bed? To catch up on his sleep.</p>
                    <p>Why don't eggs tell jokes? Because they'd crack each other up.</p>
                    <p>What did the janitor say when he jumped out of the closet? "Supplies!"</p>
                    <p>What do you call a fish wearing a bowtie? Sofishticated.</p>
                    <p>Did you hear about the restaurant called Karma? There's no menu – you get what you deserve.</p>
                    <p>Why don't ants get sick? Because they have tiny ant-bodies.</p>
                    <p>I told my wife she was drawing her eyebrows too high. She looked surprised.</p>
                    <p>I used to play piano by ear. Now I use my hands.</p>
                    <p>Why did the cookie go to the doctor? Because it felt crummy.</p>
                </div>
                <div class="card-footer text-muted">
                    <b>Back to <a href="home.php">Home</a></b>
                </div>
            </div>
        </div>
</body>
</html>