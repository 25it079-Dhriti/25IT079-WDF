// Store all students

let students = [];

let currentPage = 1;

let studentsPerPage = 4;


// Fetch JSON data

fetch("data.json")

    .then(function(response) {

        if (!response.ok) {

            throw new Error("Data could not be loaded");

        }

        return response.json();

    })

    .then(function(data) {

        students = data;

        document.getElementById("loading").style.display = "none";

        displayStudents();

    })

    .catch(function(error) {

        document.getElementById("loading").style.display = "none";

        document.getElementById("error").innerHTML =
            "Error loading student data.";

    });


// Display students

function displayStudents() {

    let list = document.getElementById("studentList");

    list.innerHTML = "";


    // Search

    let searchText =
        document.getElementById("search").value.toLowerCase();


    // Filter

    let course =
        document.getElementById("courseFilter").value;


    let filteredStudents = students.filter(function(student) {

        let matchesSearch =
            student.name.toLowerCase().includes(searchText);

        let matchesCourse =
            course == "All" || student.course == course;

        return matchesSearch && matchesCourse;

    });


    // Sort

    let sortValue =
        document.getElementById("sort").value;


    if (sortValue == "name") {

        filteredStudents.sort(function(a, b) {

            return a.name.localeCompare(b.name);

        });

    }


    if (sortValue == "year") {

        filteredStudents.sort(function(a, b) {

            return a.year - b.year;

        });

    }


    // Pagination

    let start =
        (currentPage - 1) * studentsPerPage;

    let end =
        start + studentsPerPage;


    let pageStudents =
        filteredStudents.slice(start, end);


    // Display data

    pageStudents.forEach(function(student) {

        let div = document.createElement("div");

        div.className = "student";

        div.innerHTML = `
            <h3>${student.name}</h3>
            <p>Course: ${student.course}</p>
            <p>Year: ${student.year}</p>
            <p>City: ${student.city}</p>
        `;

        list.appendChild(div);

    });


    // Page number

    document.getElementById("pageNumber").innerHTML =
        "Page " + currentPage;

}


// Search

document.getElementById("search").addEventListener(
    "input",
    function() {

        currentPage = 1;

        displayStudents();

    }
);


// Filter

document.getElementById("courseFilter").addEventListener(
    "change",
    function() {

        currentPage = 1;

        displayStudents();

    }
);


// Sort

document.getElementById("sort").addEventListener(
    "change",
    function() {

        currentPage = 1;

        displayStudents();

    }
);


// Next Page

function nextPage() {

    let totalPages =
        Math.ceil(students.length / studentsPerPage);

    if (currentPage < totalPages) {

        currentPage++;

        displayStudents();

    }

}


// Previous Page

function previousPage() {

    if (currentPage > 1) {

        currentPage--;

        displayStudents();

    }

}