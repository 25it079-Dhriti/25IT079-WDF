
const API = "api.php";

const form = document.getElementById("studentForm");
const table = document.getElementById("studentTable");
const message = document.getElementById("message");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

let students = [];

function showMessage(text, success = true) {
    message.textContent = text;
    message.style.color = success ? "green" : "red";
}

async function request(url, options = {}) {
    const response = await fetch(url, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed");
    }

    return result;
}

// READ: Fetch all students
async function loadStudents() {
    table.replaceChildren();

    const loadingRow = document.createElement("tr");
    const loadingCell = document.createElement("td");
    loadingCell.colSpan = 5;
    loadingCell.textContent = "Loading students...";
    loadingRow.appendChild(loadingCell);
    table.appendChild(loadingRow);

    try {
        const result = await request(API);
        students = result.data || [];
        displayStudents();
    } catch (error) {
        table.replaceChildren();
        showMessage(error.message, false);
    }
}

// Display students in the table
function displayStudents() {
    table.replaceChildren();

    if (students.length === 0) {
        const row = table.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 5;
        cell.textContent = "No students found.";
        return;
    }

    students.forEach(student => {
        const row = table.insertRow();

        [student.id, student.name, student.email,
        student.course].forEach(value => {
            const cell = row.insertCell();
            cell.textContent = value;
        });

        const actions = row.insertCell();

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.className = "edit";
        editBtn.onclick = () => editStudent(student.id);

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.className = "delete";
        deleteBtn.onclick = () => deleteStudent(student.id);

        actions.append(editBtn, deleteBtn);
    });
}

// CREATE and UPDATE
form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const id = document.getElementById("id").value;

    const data = {
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    if (!data.name || !data.course ||
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
        showMessage("Please enter valid details.", false);
        return;
    }

    saveBtn.disabled = true;

    try {
        const result = await request(API, {
            method: id ? "PUT" : "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(id ? { ...data, id: Number(id) } : data)
        });

        showMessage(result.message);
        resetForm();
        await loadStudents();
    } catch (error) {
        showMessage(error.message, false);
    } finally {
        saveBtn.disabled = false;
    }
});

// Load details into form for editing
function editStudent(id) {
    const student = students.find(item => Number(item.id) === Number(id));

    if (!student) return;

    document.getElementById("id").value = student.id;
    document.getElementById("name").value = student.name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    saveBtn.textContent = "Update Student";
    cancelBtn.hidden = false;
}

// DELETE
async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    try {
        const result = await request(API, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: Number(id) })
        });

        showMessage(result.message);
        await loadStudents();
    } catch (error) {
        showMessage(error.message, false);
    }
}

// Reset form
function resetForm() {
    form.reset();
    document.getElementById("id").value = "";
    saveBtn.textContent = "Add Student";
    cancelBtn.hidden = true;
}

cancelBtn.addEventListener("click", resetForm);

// Load records when page opens
loadStudents();