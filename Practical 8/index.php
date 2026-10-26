<?php

$host = "localhost";
$dbname = "StudentHub";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database Connected Successfully!<br>";

    $sql = "INSERT INTO students (name, email)
            VALUES (:name, :email)";

    $stmt = $pdo->prepare($sql);

    $name = "Rahul";
    $email = "rahul@gmail.com";

    $stmt->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    echo "Student Added Successfully!";

} catch (PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
}

?>