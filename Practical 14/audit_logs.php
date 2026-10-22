
<?php
require_once "auth.php";
requireAdmin();

$result = $conn->query(
    "SELECT a.id, u.name, a.action, a.created_at
     FROM audit_logs a
     LEFT JOIN users u ON a.user_id = u.id
     ORDER BY a.created_at DESC
     LIMIT 100"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<?php showMenu($conn); ?>

<div class="container py-4">
    <h2>Audit Logs</h2>
    <p class="text-muted">Recent recorded activities</p>

    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($log = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= htmlspecialchars($log['name'] ?? 'Deleted user') ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

</body>
</html>