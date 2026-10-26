<?php

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize data
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $age = htmlspecialchars(trim($_POST["age"]));
    $course = htmlspecialchars(trim($_POST["course"]));

    $errors = [];

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (!is_numeric($age) || $age < 16 || $age > 60) {
        $errors[] = "Enter a valid age.";
    }

    if (empty($course)) {
        $errors[] = "Please select a course.";
    }

    // If no errors, save data
    if (empty($errors)) {

        $file = fopen("students.csv", "a");

        fputcsv($file, [
            $name,
            $email,
            $phone,
            $age,
            $course
        ]);

        fclose($file);

        $message = "Registration successful! Data saved.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>

<body>

<h2>Student Registration Form</h2>

<?php

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
}

if ($message != "") {
    echo "<p style='color:green;'>$message</p>";
}

?>

<form method="POST">

    Name:
    <input type="text" name="name">
    <br><br>

    Email:
    <input type="text" name="email">
    <br><br>

    Phone:
    <input type="text" name="phone">
    <br><br>

    Age:
    <input type="number" name="age">
    <br><br>

    Course:
    <select name="course">
        <option value="">Select Course</option>
        <option value="B.Tech">B.Tech</option>
        <option value="BCA">BCA</option>
        <option value="MCA">MCA</option>
    </select>
    <br><br>

    <input type="submit" value="Register">

</form>

</body>
</html>