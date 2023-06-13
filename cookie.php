<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set session timeout
ini_set('session.gc_maxlifetime', 600); // 10 minutes

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
  <link rel="stylesheet" href="style.css" />
  <title>All About Cookies</title>
</head>

<body>
  <div class="container">
    <h1 class="text-center">All About Cookies</h1>
    <p>
      As you surf the web, you’ve likely seen pop-up messages on several websites asking you to accept their cookies. But what does that really mean, and what exactly are you agreeing to? We asked tech experts to break it down for us.
    </p>
    <img src="diet-cookie.jpg" alt="Cookies" class="img-fluid">
    <div>
    <p>
      “A cookie is a small text file that a website saves on your computer or mobile device when you visit the site,” explains Sven Taylor, tech expert and founder of Restore Privacy. “It enables the website to remember your actions and preferences (such as login, language, font size and other display preferences) over a period of time, so you don’t have to keep re-entering them whenever you come back to the site or browse from one page to another.”
    </p>
    <div>
    <h2 class="text-center">Why Do Websites Use Cookies?</h2>
    <p>
      “Cookies are used for a variety of reasons, such as remembering login details, storing items in a shopping cart, and understanding user preferences to improve the website experience,” says Taylor. “Cookies can also be used to track a user’s browsing habits for targeted advertising, which is why many websites request your permission to use cookies.”
    </p>
    <img src="cookies-user-date.png" alt="Cookies Explained" class="img-fluid">
    <div>
    <h2 class="text-center">Should You Allow Cookies?</h2>
    <p>
      It depends on your personal preferences, but there are some things to consider. “Allowing cookies will usually result in a better browsing experience, since it enables websites to remember your preferences,” explains Taylor. “However, it’s also worth noting that allowing cookies could potentially compromise your privacy, as they can be used to track your browsing habits and deliver targeted ads. It’s a trade-off between convenience and privacy.”
    </p>
    <div>
    <h2 class="text-center">How to Manage Cookies</h2>
    <p>
        “You can manage your cookie settings by adjusting your browser’s privacy settings,” says Taylor. “Most browsers offer various options for handling cookies, such as blocking all cookies, accepting only first-party cookies, or deleting all cookies when you close the browser. It’s important to note that blocking or deleting cookies can have a negative impact on your browsing experience, as some websites may not function properly without them.”
    </p>
    <img src="cookie-monster-cookie-for-you.gif" alt="Cookie Options" class="img-fluid">
    <div>
    <h2 class="text-center">Conclusion</h2>
    <p>
        In conclusion, cookies are an essential part of the modern web browsing experience, helping websites remember user preferences and delivering personalized content. However, it's important to be aware of the privacy implications of allowing cookies and to manage your cookie settings according to your preferences. By understanding what cookies are and how they work, you can make more informed decisions about your online privacy.
    </p>
    <div class="card-footer text-muted">
      <b>Back to <a href="home.php">Home</a></b>
    </div>
</body>
</html>
