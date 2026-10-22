
<?php
require_once "auth.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT id, name, password, role, status
         FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($user['status'] === 'active' &&
            password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            logAction($conn, "User logged in");

            header("Location: index.php");
            exit;
        }
    }

    $error = "Invalid credentials or inactive account.";
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 420px;">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control mb-3" required>

            <label class="form-label">Password</label>
            <input type="password" name="password"
                   class="form-control mb-3" required>

            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>