
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();

    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit("Access denied. Admins only.");
    }
}

function logAction($conn, $action) {
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action)
         VALUES (?, ?)"
    );

    $stmt->bind_param("is", $_SESSION['user_id'], $action);
    $stmt->execute();
    $stmt->close();
}

function showMenu($conn) {
    $role = $_SESSION['role'];

    $stmt = $conn->prepare(
        "SELECT title, url FROM menu_items
         WHERE allowed_role = ? ORDER BY id"
    );

    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">';
    echo '<a class="navbar-brand" href="index.php">Admin Panel</a>';
    echo '<div class="navbar-nav">';

    while ($item = $result->fetch_assoc()) {
        echo '<a class="nav-link" href="' .
            htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') .
            '">' .
            htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') .
            '</a>';
    }

    echo '<a class="nav-link" href="logout.php">Logout</a>';
    echo '</div></nav>';

    $stmt->close();
}
?>