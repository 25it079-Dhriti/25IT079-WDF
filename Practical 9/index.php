<?php

$message = "";
$messageType = "";

$file = "users.csv";

// Create CSV file if it does not exist
if (!file_exists($file)) {

    $handle = fopen($file, "w");

    fputcsv(
        $handle,
        ["id", "name", "username", "email", "password"],
        ",",
        '"',
        "\\",
        "\r\n"
    );

    fclose($handle);
}


// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Name validation
    if (!preg_match("/^[A-Za-z ]{2,50}$/", $name)) {

        $message = "Enter a valid name.";
        $messageType = "error";

    }

    // Username validation
    elseif (!preg_match("/^[A-Za-z0-9_]{3,30}$/", $username)) {

        $message = "Username must be 3-30 characters.";
        $messageType = "error";

    }

    // Email validation
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Enter a valid email.";
        $messageType = "error";

    }

    // Password validation
    elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters.";
        $messageType = "error";

    }

    else {

        // Check duplicate username and email
        $handle = fopen($file, "r");

        $duplicate = false;

        while (($row = fgetcsv($handle)) !== false) {

            // Skip header
            if ($row[0] == "id") {
                continue;
            }

            if ($row[2] == $username || $row[3] == $email) {

                $duplicate = true;
                break;
            }
        }

        fclose($handle);


        if ($duplicate) {

            $message = "Username or email already exists.";
            $messageType = "error";

        }

        else {

            // Find last ID
            $handle = fopen($file, "r");

            $lastId = 0;

            while (($row = fgetcsv($handle)) !== false) {

                if ($row[0] != "id" && is_numeric($row[0])) {

                    $lastId = max(
                        $lastId,
                        (int)$row[0]
                    );
                }
            }

            fclose($handle);


            // Create new ID
            $newId = $lastId + 1;


            // Hash password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // Add new user
            $handle = fopen($file, "a");

            fputcsv(
                $handle,
                [
                    $newId,
                    $name,
                    $username,
                    $email,
                    $hashedPassword
                ],
                ",",
                '"',
                "\\",
                "\r\n"
            );

            fclose($handle);


            $message = "Registration successful!";
            $messageType = "success";
        }
    }
}

?>


<!DOCTYPE html>

<html>

<head>

    <title>Student Registration</title>

    <style>

        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 350px;
            margin: 60px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

    </style>

</head>


<body>

<div class="container">

    <h2>User Registration</h2>


    <?php if ($message != "") { ?>

        <p class="<?php echo $messageType; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </p>

    <?php } ?>


    <form method="POST">

        <input
            type="text"
            name="name"
            placeholder="Enter Name"
            pattern="[A-Za-z ]{2,50}"
            required
        >


        <input
            type="text"
            name="username"
            placeholder="Enter Username"
            pattern="[A-Za-z0-9_]{3,30}"
            required
        >


        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >


        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            minlength="8"
            required
        >


        <button type="submit">
            Register
        </button>

    </form>

</div>

</body>

</html>