
<?php
require_once "auth.php";

if (isset($_SESSION['user_id'])) {
    logAction($conn, "User logged out");
}

$_SESSION = [];
session_destroy();

header("Location: login.php");
exit;
?>