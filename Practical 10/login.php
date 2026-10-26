<?php

session_start();

$file = "users.csv";
$message = "";

// Create CSV if it does not exist
if (!file_exists($file)) {

    $handle = fopen($file, "w");

    fputcsv($handle, [
        "id",
        "name",
        "username",
        "email",
        "password",
        "role",
        "remember_token"
    ]);

    fclose($handle);
}


// Upgrade old P9 CSV file
$rows = [];
$handle = fopen($file, "r");

while (($row = fgetcsv($handle)) !== false) {
    $rows[] = $row;
}

fclose($handle);

if (count($rows) > 0 && count($rows[0]) < 7) {

    $rows[0] = [
        "id",
        "name",
        "username",
        "email",
        "password",
        "role",
        "remember_token"
    ];

    for ($i = 1; $i < count($rows); $i++) {

        if (count($rows[$i]) < 7) {
            $rows[$i][5] = "student";
            $rows[$i][6] = "";
        }
    }

    $handle = fopen($file, "w");

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    fclose($handle);
}


// Check session timeout
if (isset($_SESSION["user_id"])) {

    if (
        isset($_SESSION["last_activity"]) &&
        time() - $_SESSION["last_activity"] > 600
    ) {

        session_unset();
        session_destroy();

        setcookie(
            "remember_token",
            "",
            time() - 3600,
            "/"
        );

        header("Location: login.php");
        exit;
    }

    $_SESSION["last_activity"] = time();
}


// Remember Me login
if (
    !isset($_SESSION["user_id"]) &&
    isset($_COOKIE["remember_token"])
) {

    $tokenHash = hash(
        "sha256",
        $_COOKIE["remember_token"]
    );

    $handle = fopen($file, "r");

    while (($row = fgetcsv($handle)) !== false) {

        if ($row[0] == "id") {
            continue;
        }

        if (
            isset($row[6]) &&
            hash_equals($row[6], $tokenHash)
        ) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $row[0];
            $_SESSION["username"] = $row[2];
            $_SESSION["role"] = $row[5];
            $_SESSION["last_activity"] = time();

            fclose($handle);

            header("Location: dashboard.php");
            exit;
        }
    }

    fclose($handle);
}


// Login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $foundUser = null;

    $handle = fopen($file, "r");

    while (($row = fgetcsv($handle)) !== false) {

        if ($row[0] == "id") {
            continue;
        }

        if ($row[2] == $username) {

            $foundUser = $row;
            break;
        }
    }

    fclose($handle);


    if (
        $foundUser &&
        password_verify(
            $password,
            $foundUser[4]
        )
    ) {

        // Regenerate session ID
        session_regenerate_id(true);

        $_SESSION["user_id"] = $foundUser[0];
        $_SESSION["username"] = $foundUser[2];
        $_SESSION["role"] = $foundUser[5];
        $_SESSION["last_activity"] = time();


        // Remember Me
        if (isset($_POST["remember"])) {

            $token = bin2hex(
                random_bytes(32)
            );

            $tokenHash = hash(
                "sha256",
                $token
            );


            // Update token in CSV
            $allRows = [];

            $handle = fopen($file, "r");

            while (($row = fgetcsv($handle)) !== false) {

                if (
                    $row[0] != "id" &&
                    $row[0] == $foundUser[0]
                ) {
                    $row[6] = $tokenHash;
                }

                $allRows[] = $row;
            }

            fclose($handle);


            $handle = fopen($file, "w");

            foreach ($allRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);


            // Save cookie for 30 days
            setcookie(
                "remember_token",
                $token,
                [
                    "expires" => time() + (30 * 24 * 60 * 60),
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax"
                ]
            );
        }


        header("Location: dashboard.php");
        exit;

    } else {

        $message = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>StudentHub Login</title>

    <style>

        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 350px;
            margin: 80px auto;
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

        .remember {
            width: auto;
        }

        button {
            width: 100%;
            padding: 10px;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            text-align: center;
        }

    </style>

</head>


<body>

<div class="container">

    <h2>StudentHub Login</h2>


    <?php if ($message != "") { ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Enter Username"
            required
        >


        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
        >


        <label>

            <input
                class="remember"
                type="checkbox"
                name="remember"
            >

            Remember Me

        </label>


        <br><br>


        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>

</html>