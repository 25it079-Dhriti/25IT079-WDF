
<?php
require_once "auth.php";
requireAdmin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token'] ?? ''
    )) {
        exit("Invalid request.");
    }

    $id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($id && $id != $_SESSION['user_id']) {
        $stmt = $conn->prepare(
            "UPDATE users
             SET status = IF(status = 'active', 'inactive', 'active')
             WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            logAction($conn, "Changed status of user ID " . $id);
        }

        $stmt->close();
    }

    header("Location: users.php");
    exit;
}

$result = $conn->query(
    "SELECT id, name, email, role, status
     FROM users ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<?php showMenu($conn); ?>

<div class="container py-4">
    <h2>Manage Users</h2>

    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <form method="POST">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="user_id"
                                   value="<?= $user['id'] ?>">
                            <button class="btn btn-sm btn-warning">
                                Toggle Status
                            </button>
                        </form>
                    <?php else: ?>
                        Current Admin
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

</body>
</html>