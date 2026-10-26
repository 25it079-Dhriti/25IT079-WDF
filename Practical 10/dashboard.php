<?php

session_start();


// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;
}


// Session timeout = 10 minutes
if (
    isset($_SESSION["last_activity"]) &&
    time() - $_SESSION["last_activity"] > 600
) {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit;
}


// Update activity time
$_SESSION["last_activity"] = time();

?>

<!DOCTYPE html>

<html>

<head>

    <title>StudentHub Dashboard</title>

    <style>

        body {
            font-family: Arial;
            background: #f2f2f2;
            text-align: center;
        }

        .box {
            width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background: black;
            color: white;
            text-decoration: none;
        }

    </style>

</head>


<body>

<div class="box">

    <h2>Welcome to StudentHub</h2>


    <p>
        Username:

        <b>
            <?php
            echo htmlspecialchars(
                $_SESSION["username"]
            );
            ?>
        </b>
    </p>


    <p>
        Role:

        <b>
            <?php
            echo htmlspecialchars(
                $_SESSION["role"]
            );
            ?>
        </b>
    </p>


    <?php if ($_SESSION["role"] == "admin") { ?>

        <h3>Admin Dashboard</h3>

        <p>
            You have administrator access.
        </p>

    <?php } else { ?>

        <h3>Student Dashboard</h3>

        <p>
            You have student access.
        </p>

    <?php } ?>


    <a href="logout.php">
        Logout
    </a>

</div>

</body>

</html>