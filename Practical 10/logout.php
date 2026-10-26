<?php

session_start();


// Remove remember-me cookie
setcookie(
    "remember_token",
    "",
    time() - 3600,
    "/"
);


// Destroy session
session_unset();
session_destroy();


// Go back to login page
header("Location: login.php");
exit;

?>