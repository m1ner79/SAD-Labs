<?php
include("config.php");

function create_hash($password) 
{
    $salt = bin2hex(random_bytes(32));
    $hash = hash('sha256', $salt . $password);
    return [
        'salt' => $salt,
        'hash' => $hash,
    ];
}

function verifyPassword($password, $salt, $hash) 
{
    $hashedInputPassword = hash('sha256', $salt . $password);
    
    if ($hashedInputPassword === $hash)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Function to escape angle brackets
function escape_angle_brackets($str) 
{
    return str_replace(array('<', '>'), array('&lt;', '&gt;'), $str);
}

function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>