<?php
require "db.php";
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id = (int) ($_POST["id"] ?? 0);
        $action = $_POST["action"];
        if ($action == "delete") {
            $q = $conn->prepare("DELETE FROM events WHERE id=?");
            $q->execute([$id]);
            $msg = "Event deleted successfully!";
        } else {
            $title = trim($_POST["title"]);
            $desc = trim($_POST["description"]);
            $date = $_POST["event_date"];
            $status = $_POST["status"];
            $poster = $_POST["old_poster"] ?? "";
            if (!$title || !$desc || !$date)
                throw new Exception("Fill all fields!");
            if ($_FILES["poster"]["error"] != 4) {
                $f = $_FILES["poster"];
                $mime = mime_content_type($f["tmp_name"]);
                if (
                    $f["size"] > 2097152 ||
                    !in_array($mime, ["image/jpeg", "image/png", "image/webp"]) ||
                    !getimagesize($f["tmp_name"])
                )
                    throw new Exception("Invalid poster! Max 2MB.");
                if (!is_dir("uploads"))
                    mkdir("uploads");
                $poster = uniqid() . ".jpg";
                if (!move_uploaded_file($f["tmp_name"], "uploads/" . $poster))
                    throw new Exception("Upload failed!");
            }
            if ($action == "update") {
                $q = $conn->prepare(
                    "UPDATE events SET title=?,description=?,
                    event_date=?,status=?,poster=? WHERE id=?"
                );
                $q->execute([$title, $desc, $date, $status, $poster, $id]);
                $msg = "Event updated successfully!";
            } else {
                $q = $conn->prepare(
                    "INSERT INTO events(title,description,event_date,status,poster)
                    VALUES(?,?,?,?,?)"
                );
                $q->execute([$title, $desc, $date, $status, $poster]);
                $msg = "Event added successfully!";
            }
        }
    } catch (Throwable $e) {
        $msg = $e instanceof PDOException
            ? "Database operation failed!" : $e->getMessage();
    }
}
$edit = null;
if (isset($_GET["edit"])) {
    $q = $conn->prepare("SELECT * FROM events WHERE id=?");
    $q->execute([(int) $_GET["edit"]]);
    $edit = $q->fetch(PDO::FETCH_ASSOC);
}
$events = $conn->query("SELECT * FROM events ORDER BY id DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
    <h2>Event Management System</h2>
    <p>
        <?= htmlspecialchars($msg) ?>
    </p>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit["id"] ?? 0 ?>">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
        <input type="hidden" name="old_poster" value="<?= htmlspecialchars($edit["poster"] ?? "") ?>">
        <input class="form-control mb-2" name="title" placeholder="Event Title" required
            value="<?= htmlspecialchars($edit["title"] ?? "") ?>">
        <textarea class="form-control mb-2" name="description" placeholder="Description"
            required><?= htmlspecialchars($edit["description"] ?? "") ?></textarea>
        <input type="date" class="form-control mb-2" name="event_date" required
            value="<?= $edit["event_date"] ?? "" ?>">
        <select class="form-select mb-2" name="status">
            <option>Open</option>
            <option <?= ($edit["status"] ?? "") == "Closed" ? "selected" : "" ?>>Closed</option>
        </select>
        <input type="file" class="form-control mb-2" name="poster" accept=".jpg,.jpeg,.png,.webp">
        <button class="btn btn-primary">
            <?= $edit ? "Update Event" : "Add Event" ?>
        </button>
    </form>
    <table class="table table-bordered mt-4">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Date</th>
            <th>Status</th>
            <th>Poster</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($events as $e): ?>
            <tr>
                <td>
                    <?= $e["id"] ?>
                </td>
                <td>
                    <?= htmlspecialchars($e["title"]) ?>
                </td>
                <td>
                    <?= $e["event_date"] ?>
                </td>
                <td>
                    <?= $e["status"] ?>
                </td>
                <td>
                    <?php if ($e["poster"]): ?>
                        <img src="uploads/<?= rawurlencode($e["poster"]) ?>" width="55" alt="Poster">
                    <?php else: ?>None
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit=<?= $e["id"] ?>">Edit</a>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="id" value="<?= $e["id"] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>