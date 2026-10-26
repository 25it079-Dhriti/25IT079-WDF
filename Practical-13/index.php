<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f8;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #263b63;
        }
        input {
            width: 95%;
            padding: 10px;
            margin: 7px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 9px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 3px;
        }
        .save {
            background: #2563eb;
            color: white;
        }
        .edit {
            background: #f59e0b;
            color: white;
        }
        .delete {
            background: #dc2626;
            color: white;
        }
        .cancel {
            background: #64748b;
            color: white;
        }

        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #263b63;
            color: white;
        }
        #message {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Management System</h2>
        <form id="studentForm">
            <input type="hidden" id="id">
            <input type="text" id="name" placeholder="Student Name" required>
            <input type="email" id="email" placeholder="Email Address" required>
            <input type="text" id="course" placeholder="Course" required>
            <button type="submit" class="save" id="saveBtn">
                Add Student
            </button>
            <button type="button" class="cancel" id="cancelBtn" hidden>
                Cancel Edit
            </button>
        </form>
        <p id="message" role="status" aria-live="polite"></p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <tr>
                        <td colspan="5">Loading students...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>