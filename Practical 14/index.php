
<?php
require_once "auth.php";
requireAdmin();

logAction($conn, "Viewed admin dashboard");

function getCount($conn, $sql) {
    $result = $conn->query($sql);
    return (int) $result->fetch_row()[0];
}

$userCount = getCount(
    $conn, "SELECT COUNT(*) FROM users"
);
$studentCount = getCount(
    $conn, "SELECT COUNT(*) FROM students"
);
$eventCount = getCount(
    $conn, "SELECT COUNT(*) FROM events"
);
$registrationCount = getCount(
    $conn, "SELECT COUNT(*) FROM registrations"
);

$latest = $conn->query(
    "SELECT s.name, e.event_name, r.registered_at
     FROM registrations r
     JOIN students s ON r.student_id = s.id
     JOIN events e ON r.event_id = e.id
     ORDER BY r.registered_at DESC
     LIMIT 5"
);

$monthly = $conn->query(
    "SELECT DATE_FORMAT(registered_at, '%Y-%m') AS month,
            COUNT(*) AS total
     FROM registrations
     GROUP BY DATE_FORMAT(registered_at, '%Y-%m')
     ORDER BY month"
);

$months = [];
$totals = [];

while ($row = $monthly->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = (int) $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<?php showMenu($conn); ?>

<div class="container py-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
    <p class="text-muted">Admin Dashboard</p>

    <div class="row g-3 mb-4">
        <?php
        $cards = [
            "Total Users" => $userCount,
            "Total Students" => $studentCount,
            "Total Events" => $eventCount,
            "Registrations" => $registrationCount
        ];

        foreach ($cards as $title => $value):
        ?>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm p-3">
                <h6><?= $title ?></h6>
                <h2><?= $value ?></h2>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm p-3 mb-4">
        <h5>Monthly Event Registrations</h5>
        <canvas id="registrationChart"></canvas>
    </div>

    <div class="card shadow-sm p-3">
        <h5>Latest Registrations</h5>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Event</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($latest->num_rows > 0): ?>
                <?php while ($row = $latest->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                    <td><?= htmlspecialchars($row['registered_at']) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No registrations found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('registrationChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Registrations',
            data: <?= json_encode($totals) ?>,
            backgroundColor: '#6c8cff'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>

</body>
</html>
