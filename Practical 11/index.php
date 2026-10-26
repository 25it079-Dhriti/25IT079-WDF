<?php
$conn = new mysqli("localhost", "root", "", "practical11");
if ($conn->connect_error)
    die("Database connection failed");
$msg = "";
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $id = $_POST['id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE students SET name=?,email=?,course=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $course, $id);
        $msg = "Student updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students(name,email,course) VALUES(?,?,?)");
        $stmt->bind_param("sss", $name, $email, $course);
        $msg = "Student added successfully!";
    }
    $stmt->execute();
}
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $msg = "Student deleted successfully!";
}
$edit = ['id' => '', 'name' => '', 'email' => '', 'course' => ''];
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $result = $conn->query("SELECT * FROM students WHERE id=$id");
    if ($result->num_rows)
        $edit = $result->fetch_assoc();
}
$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM students
    WHERE name LIKE ? OR email LIKE ? OR course LIKE ?");
$term = "%$search%";
$stmt->bind_param("sss", $term, $term, $term);
$stmt->execute();
$students = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Student Management</title>
    <style>
        body {
            font-family: Arial;
            margin: 30px;
            background: #f4f4f4
        }

        form,
        .box {
            background: white;
            padding: 15px;
            margin-bottom: 15px
        }

        input {
            padding: 8px;
            margin: 5px
        }

        button {
            padding: 8px;
            background: #2878d0;
            color: white;
            border: 0
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse
        }

        td,
        th {
            padding: 10px;
            border: 1px solid #ccc
        }

        a {
            margin: 5px
        }
    </style>
</head>

<body>
    <h2>Student Management System</h2>
    <p><?= htmlspecialchars($msg) ?></p>
    <form method="post">
        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
        <input name="name" placeholder="Student Name" value="<?= htmlspecialchars($edit['name']) ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($edit['email']) ?>" required>
        <input name="course" placeholder="Course" value="<?= htmlspecialchars($edit['course']) ?>" required>
        <button name="save">Save Student</button>
    </form>
    <form method="get">
        <input name="search" placeholder="Search name, email or course" value="<?= htmlspecialchars($search) ?>">
        <button>Search</button>
        <a href="index.php">Clear</a>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>
        <?php while ($s = $students->fetch_assoc()): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['course']) ?></td>
                <td>
                    <a href="?edit=<?= $s['id'] ?>">Edit</a>
                    <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>