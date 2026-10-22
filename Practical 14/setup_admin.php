
<?php
require_once "db.php";

$name = "Admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    echo "Admin created successfully!";
} else {
    echo "Admin already exists or could not be created.";
}

$stmt->close();
?>