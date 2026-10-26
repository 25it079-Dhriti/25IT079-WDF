<?php
header("Content-Type: application/json");

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];

function respond($success, $message, $data = null, $status = 200)
{
    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

try {
    // READ: Get all students
    if ($method === "GET") {
        $stmt = $conn->query(
            "SELECT * FROM students ORDER BY id DESC"
        );

        respond(
            true,
            "Students fetched successfully",
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    // Read JSON input for POST, PUT and DELETE
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        $input = [];
    }

    // CREATE: Add a student
    if ($method === "POST") {
        $name = trim($input["name"] ?? "");
        $email = trim($input["email"] ?? "");
        $course = trim($input["course"] ?? "");

        if (
            $name === "" || $course === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            respond(false, "Enter valid student details", null, 400);
        }

        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, course)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$name, $email, $course]);

        respond(true, "Student added successfully", [
            "id" => (int) $conn->lastInsertId(),
            "name" => $name,
            "email" => $email,
            "course" => $course
        ], 201);
    }

    // UPDATE: Edit a student
    if ($method === "PUT") {
        $id = filter_var(
            $input["id"] ?? null,
            FILTER_VALIDATE_INT
        );

        $name = trim($input["name"] ?? "");
        $email = trim($input["email"] ?? "");
        $course = trim($input["course"] ?? "");

        if (
            !$id || $id < 1 || $name === "" ||
            $course === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            respond(false, "Enter valid student details", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE students
             SET name = ?, email = ?, course = ?
             WHERE id = ?"
        );

        $stmt->execute([$name, $email, $course, $id]);

        if ($stmt->rowCount() === 0) {
            $check = $conn->prepare(
                "SELECT id FROM students WHERE id = ?"
            );
            $check->execute([$id]);

            if (!$check->fetch()) {
                respond(false, "Student not found", null, 404);
            }
        }

        respond(true, "Student updated successfully");
    }

    // DELETE: Remove a student
    if ($method === "DELETE") {
        $id = filter_var(
            $input["id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            respond(false, "Valid student ID is required", null, 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM students WHERE id = ?"
        );
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            respond(false, "Student not found", null, 404);
        }

        respond(true, "Student deleted successfully");
    }

    header("Allow: GET, POST, PUT, DELETE");
    respond(false, "Method not allowed", null, 405);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ]);
    exit;
}
?>